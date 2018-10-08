<?php

namespace App\Infrastructure\Queue;

use Dtc\QueueBundle\Entity\JobArchive as BaseJobArchive;
use Doctrine\ORM\Mapping as ORM;
use Dtc\GridBundle\Annotation as Grid;

/**
 * Class JobArchive.
 *
 * @ORM\Entity
 * @ORM\Table(name="dtc_queue_job_archive",indexes={
 *                  @ORM\Index(name="job_archive_status_idx", columns={"status"}),
 *                  @ORM\Index(name="job_archive_updated_at_idx", columns={"updated_at"})})
 *
 * @Grid\Grid(actions={@Grid\ShowAction()},sort=@Grid\Sort(column="updatedAt",direction="DESC"))
 */
class JobArchive extends BaseJobArchive
{
}
