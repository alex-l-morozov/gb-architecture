<?php

/**
 * Class DeveloperJobs
 */
class DeveloperJobs implements SplSubject
{
    use TSingletone;

    private array $jobs;
    private array $observers;
    public object $lastAddedJob;

    /**
     * @param SplObserver $observer
     * @return void
     */
    public function attach(SplObserver $observer)
    {
        $this->observers[$observer->language][] = $observer;
    }

    /**
     * @param SplObserver $observer
     * @return void
     */
    public function detach(SplObserver $observer)
    {
        foreach ($this->observers[$observer->language] as $key => $value){
            if ($value === $observer){
                unset($this->observers[$observer->language][$key]);
                return;
            }
        }
    }

    /**
     * @return void
     */
    public function notify()
    {
        $data = $this->lastAddedJob;
        if (!isset($data)){
            echo 'Error';
            return;
        }
        foreach ($this->observers[$data->language] as $observer){
            $observer->update($this);
        }
    }

    /**
     * @param Job $job
     * @return void
     */
    public function addJob(Job $job){
        $this->jobs[$job->language][] = $job;
        $this->lastAddedJob = $job;
        $this->notify();
    }
}
