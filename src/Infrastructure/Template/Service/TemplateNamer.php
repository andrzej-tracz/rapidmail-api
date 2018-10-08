<?php

namespace App\Infrastructure\Template\Service;

use App\Domain\Template\Template;
use Vich\UploaderBundle\Mapping\PropertyMapping;

class TemplateNamer
{
    /**
     * Creates a name for the file being uploaded.
     *
     * @param object \App\TemplatesBundle\Entity\Template The object the upload is attached to
     * @param PropertyMapping $mapping The mapping to use to manipulate the given object
     *
     * @return string The file name
     */
    public function name($object, PropertyMapping $mapping): string
    {
        if (false == $object instanceof Template) {
            throw new \InvalidArgumentException(sprintf(
                "TemplateNamerService supports instances of App\TemplatesBundle\Entity\Template class %s given",
                get_class($object)
            ));
        }

        /** @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
        $file = $object->getArchiveFile();
        $extension = $file->guessExtension();
        $filename = str_replace(".{$extension}", '', $file->getClientOriginalName());
        $date = (new \DateTime())->format('d-M-Y-His');

        return "{$filename}-{$date}.{$extension}";
    }
}
