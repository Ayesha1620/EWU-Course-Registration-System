<?php

// ============ JSON response helpers ============

// সব API response JSON আকারে দেয়, তাই common format এক জায়গায় বাঁধা
function json_response($data, $httpCode = 200): void
{
    http_response_code($httpCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

function json_success($data = [], $message = 'Success', $httpCode = 200): void
{
    json_response([
        'status'  => 'success',
        'message' => $message,
        'data'    => $data,
    ], $httpCode);
}

function json_error($message, $httpCode = 400, $data = []): void
{
    json_response([
        'status'  => 'error',
        'message' => $message,
        'data'    => $data,
    ], $httpCode);
}

// ============ Request body helpers ============

// POST/PUT request এর JSON body কে array করে দেয়
function get_json_input(): array
{
    $raw  = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}