<?php
function h($s)
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function h_digit($d)
{
    if (0 === $d) {
        return '';
    }
    return h((string) $d);
}