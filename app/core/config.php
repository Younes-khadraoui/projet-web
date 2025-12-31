<?php
// app/core/config.php

class Config {
    private static $config = [];
    private static $loaded = false;

    public static function load() {
        if (self::$loaded) {
            return;
        }

        // Load .env file
        $env_file = __DIR__ . '/../../.env';
        
        if (file_exists($env_file)) {
            $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                // Skip comments
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                
                // Parse KEY=VALUE
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    
                    // Remove quotes if present
                    $value = trim($value, '"\'');
                    
                    self::$config[$key] = $value;
                }
            }
        }

        self::$loaded = true;
    }

    public static function get($key, $default = null) {
        if (!self::$loaded) {
            self::load();
        }
        
        return array_key_exists($key, self::$config) ? self::$config[$key] : $default;
    }

    public static function all() {
        if (!self::$loaded) {
            self::load();
        }
        
        return self::$config;
    }

    public static function isDev() {
        return self::get('APP_ENV', 'development') === 'development';
    }

    public static function isProd() {
        return self::get('APP_ENV', 'development') === 'production';
    }
}

Config::load();
