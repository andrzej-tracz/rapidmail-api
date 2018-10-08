<?php

namespace App\Infrastructure\Queue;

use Dtc\QueueBundle\Entity\Job as BaseJob;
use Doctrine\ORM\Mapping as ORM;
use Dtc\GridBundle\Annotation as Grid;

/**
 * Class Job.
 *
 * @ORM\Entity
 * @ORM\Table(name="dtc_queue_job", indexes={@ORM\Index(name="job_crc_hash_idx", columns={"crcHash","status"}),
 *                  @ORM\Index(name="job_priority_idx", columns={"priority","whenAt"}),
 *                  @ORM\Index(name="job_when_idx", columns={"whenAt"}),
 *                  @ORM\Index(name="job_status_idx", columns={"status","whenAt"})})
 *
 * @Grid\Grid(actions={@Grid\ShowAction(), @Grid\DeleteAction(label="Archive")},sort=@Grid\Sort(column="id"))
 */
class Job extends BaseJob
{
}
