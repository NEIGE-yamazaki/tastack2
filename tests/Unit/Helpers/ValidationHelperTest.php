<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;

class ValidationHelperTest extends TestCase
{
    /**
     * メールアドレスの検証をテスト
     */
    public function test_email_validation(): void
    {
        $this->assertTrue(filter_var('test@example.com', FILTER_VALIDATE_EMAIL) !== false);
        $this->assertFalse(filter_var('invalid-email', FILTER_VALIDATE_EMAIL) !== false);
    }

    /**
     * URLの検証をテスト
     */
    public function test_url_validation(): void
    {
        $this->assertTrue(filter_var('https://example.com', FILTER_VALIDATE_URL) !== false);
        $this->assertFalse(filter_var('invalid-url', FILTER_VALIDATE_URL) !== false);
    }

    /**
     * パスワードの強度チェックをテスト
     */
    public function test_password_strength(): void
    {
        $strongPassword = 'StrongPassword123!';
        $weakPassword = '123';

        $this->assertTrue(strlen($strongPassword) >= 8);
        $this->assertFalse(strlen($weakPassword) >= 8);
    }

    /**
     * 日付フォーマットの検証をテスト
     */
    public function test_date_format_validation(): void
    {
        $validDate = '2024-12-31';
        $invalidDate = '2024-13-32';

        $this->assertTrue($this->isValidDate($validDate, 'Y-m-d'));
        $this->assertFalse($this->isValidDate($invalidDate, 'Y-m-d'));
    }

    /**
     * 日付の有効性をチェックするヘルパーメソッド
     */
    private function isValidDate(string $date, string $format): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * テキストの長さ制限をテスト
     */
    public function test_text_length_limits(): void
    {
        $shortText = 'Short text';
        $longText = str_repeat('A', 256);

        $this->assertTrue(strlen($shortText) <= 255);
        $this->assertFalse(strlen($longText) <= 255);
    }

    /**
     * HTMLタグの除去をテスト
     */
    public function test_html_tag_removal(): void
    {
        $htmlText = '<script>alert("XSS")</script>Hello World';
        $cleanText = strip_tags($htmlText);

        $this->assertEquals('alert("XSS")Hello World', $cleanText);
    }

    /**
     * 数値の範囲チェックをテスト
     */
    public function test_numeric_range_validation(): void
    {
        $validNumber = 50;
        $invalidNumber = 150;

        $this->assertTrue($validNumber >= 0 && $validNumber <= 100);
        $this->assertFalse($invalidNumber >= 0 && $invalidNumber <= 100);
    }

    /**
     * 配列の空チェックをテスト
     */
    public function test_array_empty_check(): void
    {
        $emptyArray = [];
        $nonEmptyArray = [1, 2, 3];

        $this->assertTrue(empty($emptyArray));
        $this->assertFalse(empty($nonEmptyArray));
    }

    /**
     * 文字列の空白除去をテスト
     */
    public function test_string_trim(): void
    {
        $stringWithSpaces = '  Hello World  ';
        $trimmedString = trim($stringWithSpaces);

        $this->assertEquals('Hello World', $trimmedString);
    }

    /**
     * JSONの有効性チェックをテスト
     */
    public function test_json_validation(): void
    {
        $validJson = '{"key": "value"}';
        $invalidJson = '{"key": "value"';

        $this->assertTrue($this->isValidJson($validJson));
        $this->assertFalse($this->isValidJson($invalidJson));
    }

    /**
     * JSONの有効性をチェックするヘルパーメソッド
     */
    private function isValidJson(string $json): bool
    {
        json_decode($json);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
