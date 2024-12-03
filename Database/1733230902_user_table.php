<?php

return [
    'name' => 'user',
    'columns' => [
        'id INT AUTO_INCREMENT PRIMARY KEY',
        'name VARCHAR(255) NOT NULL',
        'is_active TINYINT(1) DEFAULT 1',
        'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
    ]
];