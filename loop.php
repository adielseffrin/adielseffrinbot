<?php

require './vendor/autoload.php';

use React\EventLoop\Factory as EventLoopFactory;
use React\EventLoop\LoopInterface;
use React\Stream\ReadableResourceStream as ReadableResourceStream;
use React\Filesystem\Filesystem as Filesystem;

$loop = EventLoopFactory::create();
// $filesystem = Filesystem::create($loop);

// $stream = new ReadableResourceStream(
//   fopen('numeros.txt', 'rb'),
//   $loop
// );

// $stream->on('data', function ($chunk) {
//   // echo "$chunk";
// });

// $stream->on('end', function () {
//   $this->mem1 = (memory_get_peak_usage(true) / 1024 / 1024);
// });

// $filesystem->file('numeros.txt')->open('rb')->then(function ($stream) {
//   $stream->on('data', function ($chunk) {
//     echo "$chunk";
//   });

//   $stream->on('end', function () {
//     $mem2 = (memory_get_peak_usage(true) / 1024 / 1024);
//     echo $this->mem1 . " // " . $mem2;
//   });
// });




saySomething($loop);

$loop->run();

function saySomething(LoopInterface $loop)
{
  $time = rand(1, 5);
  $loop->addTimer($time, function () use ($loop, $time) {
    echo "esperei " . $time . "segundos.";
    saySomething($loop);
  });
}
