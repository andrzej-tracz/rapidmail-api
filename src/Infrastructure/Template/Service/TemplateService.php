<?php

namespace App\Infrastructure\Template\Service;

use App\Infrastructure\Template\PhantomJS\Converter;
use App\Domain\Template\Exceptions\ArchiveNotReadableException;
use App\Domain\Template\Exceptions\InvalidArchiveException;
use App\Domain\Template\Schema\TemplateSchema;
use App\Domain\Template\Template;
use App\Domain\Template\TemplateSection;
use App\Infrastructure\Template\Repository\TemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\DomCrawler\Crawler;

class TemplateService
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var Finder
     */
    protected $finder;

    /**
     * @var string
     */
    protected $extractPath;

    /**
     * Public path for template assets (images etc).
     *
     * @var string
     */
    protected $publicPath;

    /**
     * @var
     */
    public $repository;

    protected $log;

    /**
     * TemplateService constructor.
     *
     * @param ContainerInterface     $container
     * @param EntityManagerInterface $em
     */
    public function __construct(
        ContainerInterface $container,
        EntityManagerInterface $em,
        TemplateRepository $templates,
        LoggerInterface $log
    ) {
        $this->container = $container;
        $this->em = $em;
        $this->fs = new Filesystem();
        $this->finder = new Finder();
        $this->extractPath = $this->container->getParameter('extracted_templates_path');
        $this->publicPath = $this->container->getParameter('templates_public_path');
        $this->repository = $templates;
        $this->log = $log;
    }

    /**
     * When template is created via uploaded ZIP archive with html file and images
     * we must extract archive, move images and insert sections to database from HTML file.
     *
     * @param Template $template
     *
     * @throws InvalidArchiveException
     */
    public function importTemplateFromArchive(Template $template)
    {
        $this->em->persist($template);
        $this->em->flush();

        $this->extractArchive($template);

        $path = $this->findTemplateHTMLFile($template);

        $this->parseTemplateContents($template, $path);

        $this->clearExtractedFiles($template);
    }

    /**
     * Returns path to uploaded template archive zip.
     *
     * @param Template $template
     *
     * @return string
     */
    public function getArchiveFilePath(Template $template)
    {
        return $this->container->get('kernel')->getRootDir()
            .'/../var/storage/templates/'
            .$template->getArchive()->getName();
    }

    /**
     * Return path to extracted templates directory.
     *
     * @param Template $template
     *
     * @return string
     */
    public function getTemplateExtractPath(Template $template)
    {
        return $this->extractPath.DIRECTORY_SEPARATOR.$template->getId();
    }

    public function getTemplateAssetsPublicPath(Template $template)
    {
        return $this->publicPath.DIRECTORY_SEPARATOR.$template->getName();
    }

    public function getTemplateAssetsPublicUrl(Template $template)
    {
        $base = $this->container->getParameter('assets_base_url');

        return "{$base}assets/templates/{$template->getName()}/";
    }

    /**
     * Search for HTML file in extracted archive and returns contents from this file if exists.
     *
     * @param Template $template
     *
     * @return string
     *
     * @throws InvalidArchiveException
     */
    public function findTemplateHTMLFile(Template $template)
    {
        $path = $this->getTemplateExtractPath($template);
        $dirs = $this->finder->in($path)->depth(0)->directories();

        if ($dirs->count() > 1) {
            throw new InvalidArchiveException('Invalid structure of ZIP file');
        }

        $name = '';
        foreach ($dirs as $key => $dir) {
            if ($key > 0) {
                break;
            }
            $name = str_replace($path.'/', '', $dir);
        }

        $assets = $this->finder->in("{$path}/{$name}/")->depth(0)->directories();

        if ($assets->count()) {
            foreach ($assets as $asset) {
                /** @var $asset \Symfony\Component\Finder\SplFileInfo */
                if ($asset->getFilename() == $name) {
                    continue;
                }

                $this->fs->mirror(
                    $asset->getPath(),
                    $this->getTemplateAssetsPublicPath($template)
                );
            }
        }

        $files = $this->finder->in("{$path}/{$name}")->files();

        /** @var $file \Symfony\Component\Finder\SplFileInfo */
        $file = $files->getIterator()->current();

        return $file->getContents();
    }

    /**
     * Parse template sections from purge html contents.
     *
     * @param Template $template
     * @param $html
     */
    public function parseTemplateContents(Template $template, $html)
    {
        $baseUrl = $this->getTemplateAssetsPublicUrl($template);

        $html = preg_replace('/src="(.*?)"/', 'src="'.$baseUrl.'$1"', $html);
        $html = preg_replace('/background="(.*?)"/', 'background="'.$baseUrl.'$1"', $html);

        $crawler = new Crawler($html);

        $crawler
            ->filter(TemplateSchema::SELECTOR_SECTION)
            ->each(
                function (Crawler $node, $key) use ($template) {
                    $section = $node->getNode(0);
                    $name = $node->attr(TemplateSchema::ATTRIBUTE_SECTION_NAME) ?: $this->defaultSectionName()."-$key";
                    $html = $section->ownerDocument->saveHTML($section);

                    $this->saveTemplateSection($template, $html, $name);

                    $section->parentNode->removeChild($section);
                }
            );

        $preheader = $crawler->filter(TemplateSchema::SELECTOR_PREHEADER)->first()->getNode(0);
        $preheaderHtml = $preheader->ownerDocument->saveHTML($preheader);
        $template->setPreheader($preheaderHtml);
        $preheader->parentNode->removeChild($preheader);

        $styles = $crawler->filter('head style')->first();
        $template->setHeadStyles($styles->html());

        $stylesNode = $styles->getNode(0);
        $stylesNode->parentNode->removeChild($stylesNode);

        $document = $crawler->getNode(0);
        $html = $document->ownerDocument->saveHTML($document);

        $patterns = [
            '/<!--(.*)-->\n/',
            '/<\/body>/',
        ];

        $replacements = [
            '',
            "\n{{BODY}}\n</body>",
        ];

        $html = preg_replace($patterns, $replacements, $html);

        $template->setLayoutHtml($html);
        $this->em->persist($template);
        $this->em->flush();
    }

    /**
     * @param Template $template
     * @param $html
     * @param null $name
     *
     * @return TemplateSection
     */
    public function saveTemplateSection(Template $template, $html, $name = null)
    {
        $sectionsRepository = $this->em->getRepository(TemplateSection::class);
        $section = $sectionsRepository->findOneBy(
            [
                'template' => $template,
                'name' => $name,
            ]
        );

        if (!$section) {
            $section = new TemplateSection();
        }

        $section->setName($name);
        $section->setContents($html);
        $section->setTemplate($template);
        $template->addSection($section);

        $this->em->persist($section);
        $this->em->flush();

        return $section;
    }

    /**
     * @param Template $template
     */
    public function generateTemplateScreenShot(Template $template)
    {
        $sections = $template->getSections();
        $header = $template->getLayout()->getHeader();
        $footer = $template->getLayout()->getFooter();

        $bodySections = $sections->map(
            function (TemplateSection $section) {
                return $section->getContents();
            }
        )->toArray();

        $body = join("\n", $bodySections);
        $html = $header.$body.$footer;

        $publicPath = $this->getTemplateAssetsPublicPath($template);

        $converter = new Converter();
        $converter
            ->addPage($html)
            ->toPng()
            ->save("{$publicPath}/screenshot.png");
    }

    /**
     * @param Template        $template
     * @param TemplateSection $section
     */
    public function generateSectionThumbnail(TemplateSection $section)
    {
        $template = $section->getTemplate();
        $publicPath = $this->getTemplateAssetsPublicPath($template);
        $publicUrl = $this->getTemplateAssetsPublicUrl($template);
        $header = $template->getLayout()->getHeader();
        $body = $section->getContents();
        $footer = $template->getLayout()->getFooter();
        $name = $section->getName();

        $html = $header.$body.$footer;

        $converter = new Converter();
        $converter
            ->addPage($html)
            ->toPng()
            ->save("{$publicPath}/sections/{$name}.png");

        $section->setThumbnail("{$publicUrl}/sections/{$name}.png");

        $this->em->persist($section);
        $this->em->flush();
    }

    public function defaultSectionName()
    {
        return 'section';
    }

    /**
     * @param Template $template
     */
    protected function extractArchive(Template $template)
    {
        $zip = new \ZipArchive();
        $path = $this->getArchiveFilePath($template);
        $extractPath = $this->getTemplateExtractPath($template);

        if (false == $zip->open($path)) {
            throw new ArchiveNotReadableException($path);
        }

        if (!$this->fs->exists($extractPath)) {
            $this->fs->mkdir($extractPath);
        }

        $zip->extractTo($extractPath);
        $zip->close();
    }

    protected function clearExtractedFiles(Template $template)
    {
        $extractPath = $this->getTemplateExtractPath($template);

        if ($this->fs->exists($extractPath)) {
            $this->fs->remove($extractPath);
        }
    }
}
