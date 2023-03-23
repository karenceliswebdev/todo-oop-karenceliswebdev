<?php

/**
 * https://heroicons.com/
 */
function svg(string $name): string
{
    return file_get_contents("./resources/svg/{$name}.svg");
}

function getLine(App\Models\Todo $todo): string  //ipv object want dan eender welk object meegegeven kunnen worden
{
    return $todo->isDone() ? ' line-through' : '';
}
