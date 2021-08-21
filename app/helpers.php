<?php

function extractNameFrom($email) {
    $nameArr = explode('@', $email);
    $nameArr = explode('.', $nameArr[0]);
    return $nameArr[0];
}