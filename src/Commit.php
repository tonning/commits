<?php

namespace Tonning\Commits;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Commit implements Arrayable, Jsonable
{
    protected $hash;
    protected $author;
    protected $message = [];
    protected $date;
    protected $merge;
    protected $url;

    /**
     * @param mixed $date
     *
     * @return Commit
     */
    public function setDate($date)
    {
        $this->date = Carbon::parse(trim(str_replace('Date: ', '', $date)));
    }

    /**
     * @param mixed $message
     *
     * @return Commit
     */
    public function setMessageLine($message)
    {
        $this->message[] = trim($message);
    }

    /**
     * @param mixed $author
     *
     * @return Commit
     */
    public function setAuthor($author)
    {
        $this->author = str_replace('Author: ', '', $author);
    }

    /**
     * @param mixed $hash
     *
     * @return Commit
     */
    public function setHash($hash)
    {
        $this->hash = str_replace('commit ', '', $hash);
    }

    /**
     * @param mixed $merge
     *
     * @return Commit
     */
    public function setMerge($merge)
    {
        $this->merge = explode(' ', substr($merge, strlen('Merge:') + 1));
    }

    /**
     * @return mixed
     */
    public function hash()
    {
        return $this->hash;
    }

    /**
     * @return mixed
     */
    public function author()
    {
        return $this->author;
    }

    public function commitMessage()
    {
        return $this->message[0] ?? '-- No commit message --';
    }

    public function details()
    {
        $details = array_splice($this->message, 2);

        return implode("\n", $details);
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return implode("\n", $this->message);
    }

    /**
     * @return mixed
     */
    public function date()
    {
        return $this->date;
    }

    public function url()
    {
        return config('commits.repository_url') . '/commit/' . $this->hash;
    }

    public function toArray()
    {
        return [
            'hash' => $this->hash(),
            'author' => $this->author(),
            'subject' => $this->commitMessage(),
            'details' => $this->details(),
            'date' => $this->date->toDayDateTimeString(),
            'url' => $this->url(),
        ];
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}
