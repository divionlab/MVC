<?php
namespace Core\Http;

class Response
{
    public function __construct(
        protected string $content,
        protected int $status = 200
    ) {
    }
    public function send()
    {
        http_response_code($this->status);
        echo $this->content;
    }
}