<?php

namespace App\Helpers;

class SlugHelper
{
  public static function convertToSlug($string)
  {
    $string = mb_strtolower($string, 'UTF-8');

    // Replace Vietnamese characters with their ASCII equivalents
    $string = preg_replace('/[áàảãạ]/u', 'a', $string);
    $string = preg_replace('/[ắằẳẵặ]/u', 'a', $string);
    $string = preg_replace('/[ấầẩẫậâ]/u', 'a', $string);
    $string = preg_replace('/[éèẻẽẹ]/u', 'e', $string);
    $string = preg_replace('/[ếềểễệê]/u', 'e', $string);
    $string = preg_replace('/[íìỉĩị]/u', 'i', $string);
    $string = preg_replace('/[óòỏõọ]/u', 'o', $string);
    $string = preg_replace('/[ốồổỗộô]/u', 'o', $string);
    $string = preg_replace('/[ớờởỡợơ]/u', 'o', $string);
    $string = preg_replace('/[úùủũụ]/u', 'u', $string);
    $string = preg_replace('/[ứừửữựư]/u', 'u', $string);
    $string = preg_replace('/[ýỳỷỹỵ]/u', 'y', $string);
    $string = preg_replace('/[đ]/u', 'd', $string); // Handle 'đ'

    // Remove special characters
    $string = preg_replace('/[^a-z0-9\s-]/u', '', $string);

    // Replace spaces and multiple hyphens with a single hyphen
    $string = trim($string);
    $string = preg_replace('/\s+/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);

    return $string;
  }
}
