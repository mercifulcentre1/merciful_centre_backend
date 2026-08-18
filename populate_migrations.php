<?php

$dir = __DIR__;

$migrations = glob($dir . '/database/migrations/*_create_*_table.php');
foreach ($migrations as $file) {
    $content = file_get_contents($file);
    if (strpos($file, 'create_admin_users_table') !== false) {
        $schema = <<<EOT
            \$table->id();
            \$table->string('username', 50)->unique();
            \$table->string('email')->unique();
            \$table->string('full_name');
            \$table->string('password_hash');
            \$table->enum('role', ['admin', 'super_admin'])->default('admin');
            \$table->dateTime('last_login')->nullable();
            \$table->timestamps();
EOT;
        $content = preg_replace('/Schema::create.*?function \(Blueprint \$table\) \{.*?\};/s', "Schema::create('admin_users', function (Blueprint \$table) {\n$schema\n        });", $content);
        file_put_contents($file, $content);
    }
    elseif (strpos($file, 'create_sermons_table') !== false) {
        $schema = <<<EOT
            \$table->id();
            \$table->string('title');
            \$table->string('preacher');
            \$table->date('date');
            \$table->text('description')->nullable();
            \$table->string('audio_url')->nullable();
            \$table->string('thumbnail_url')->nullable();
            \$table->timestamps();
EOT;
        $content = preg_replace('/Schema::create.*?function \(Blueprint \$table\) \{.*?\};/s', "Schema::create('sermons', function (Blueprint \$table) {\n$schema\n        });", $content);
        file_put_contents($file, $content);
    }
    elseif (strpos($file, 'create_events_table') !== false) {
        $schema = <<<EOT
            \$table->id();
            \$table->string('title');
            \$table->dateTime('event_date');
            \$table->string('location')->nullable();
            \$table->text('description')->nullable();
            \$table->string('image_url')->nullable();
            \$table->timestamps();
EOT;
        $content = preg_replace('/Schema::create.*?function \(Blueprint \$table\) \{.*?\};/s', "Schema::create('events', function (Blueprint \$table) {\n$schema\n        });", $content);
        file_put_contents($file, $content);
    }
    elseif (strpos($file, 'create_galleries_table') !== false) {
        $schema = <<<EOT
            \$table->id();
            \$table->string('title');
            \$table->text('description')->nullable();
            \$table->string('image_url');
            \$table->string('category', 50)->nullable();
            \$table->foreignId('uploaded_by')->nullable()->constrained('admin_users')->nullOnDelete();
            \$table->timestamps();
EOT;
        $content = preg_replace('/Schema::create.*?function \(Blueprint \$table\) \{.*?\};/s', "Schema::create('gallery', function (Blueprint \$table) {\n$schema\n        });", $content);
        file_put_contents($file, $content);
    }
    elseif (strpos($file, 'create_settings_table') !== false) {
        $schema = <<<EOT
            \$table->string('setting_key', 50)->primary();
            \$table->text('setting_value')->nullable();
            \$table->timestamps();
EOT;
        $content = preg_replace('/Schema::create.*?function \(Blueprint \$table\) \{.*?\};/s', "Schema::create('settings', function (Blueprint \$table) {\n$schema\n        });", $content);
        file_put_contents($file, $content);
    }
    elseif (strpos($file, 'create_contact_messages_table') !== false) {
        $schema = <<<EOT
            \$table->id();
            \$table->string('name', 100);
            \$table->string('email', 100);
            \$table->string('subject');
            \$table->text('message');
            \$table->enum('status', ['new', 'read', 'replied'])->default('new');
            \$table->timestamps();
EOT;
        $content = preg_replace('/Schema::create.*?function \(Blueprint \$table\) \{.*?\};/s', "Schema::create('contact_messages', function (Blueprint \$table) {\n$schema\n        });", $content);
        file_put_contents($file, $content);
    }
    elseif (strpos($file, 'create_livestream_settings_table') !== false) {
        $schema = <<<EOT
            \$table->id();
            \$table->enum('platform', ['youtube', 'facebook']);
            \$table->string('channel_url');
            \$table->string('stream_title')->nullable();
            \$table->text('stream_description')->nullable();
            \$table->boolean('is_live')->default(false);
            \$table->dateTime('next_service_date')->nullable();
            \$table->string('next_service_title')->nullable();
            \$table->text('next_service_description')->nullable();
            \$table->timestamps();
EOT;
        $content = preg_replace('/Schema::create.*?function \(Blueprint \$table\) \{.*?\};/s', "Schema::create('livestream_settings', function (Blueprint \$table) {\n$schema\n        });", $content);
        file_put_contents($file, $content);
    }
    elseif (strpos($file, 'create_livestream_archives_table') !== false) {
        $schema = <<<EOT
            \$table->id();
            \$table->string('title');
            \$table->enum('platform', ['youtube', 'facebook'])->default('youtube');
            \$table->string('video_id');
            \$table->string('thumbnail_url')->nullable();
            \$table->dateTime('stream_date');
            \$table->timestamps();
EOT;
        $content = preg_replace('/Schema::create.*?function \(Blueprint \$table\) \{.*?\};/s', "Schema::create('livestream_archives', function (Blueprint \$table) {\n$schema\n        });", $content);
        file_put_contents($file, $content);
    }
}

$models = [
    'AdminUser' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Foundation\Auth\User as Authenticatable;\nuse Illuminate\Notifications\Notifiable;\nuse Laravel\Sanctum\HasApiTokens;\n\nclass AdminUser extends Authenticatable\n{\n    use HasApiTokens, Notifiable;\n\n    protected \$table = 'admin_users';\n    protected \$fillable = ['username', 'email', 'full_name', 'password_hash', 'role', 'last_login'];\n    protected \$hidden = ['password_hash'];\n\n    public function getAuthPasswordName()\n    {\n        return 'password_hash';\n    }\n}\n",
    'Sermon' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass Sermon extends Model\n{\n    protected \$fillable = ['title', 'preacher', 'date', 'description', 'audio_url', 'thumbnail_url'];\n}\n",
    'Event' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass Event extends Model\n{\n    protected \$fillable = ['title', 'event_date', 'location', 'description', 'image_url'];\n    protected function casts(): array\n    {\n        return [\n            'event_date' => 'datetime',\n        ];\n    }\n}\n",
    'Gallery' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass Gallery extends Model\n{\n    protected \$table = 'gallery';\n    protected \$fillable = ['title', 'description', 'image_url', 'category', 'uploaded_by'];\n\n    public function uploader()\n    {\n        return \$this->belongsTo(AdminUser::class, 'uploaded_by');\n    }\n}\n",
    'Setting' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass Setting extends Model\n{\n    protected \$primaryKey = 'setting_key';\n    public \$incrementing = false;\n    protected \$keyType = 'string';\n    protected \$fillable = ['setting_key', 'setting_value'];\n}\n",
    'ContactMessage' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass ContactMessage extends Model\n{\n    protected \$fillable = ['name', 'email', 'subject', 'message', 'status'];\n}\n",
    'LivestreamSetting' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass LivestreamSetting extends Model\n{\n    protected \$fillable = ['platform', 'channel_url', 'stream_title', 'stream_description', 'is_live', 'next_service_date', 'next_service_title', 'next_service_description'];\n    protected function casts(): array\n    {\n        return [\n            'is_live' => 'boolean',\n            'next_service_date' => 'datetime',\n        ];\n    }\n}\n",
    'LivestreamArchive' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass LivestreamArchive extends Model\n{\n    protected \$fillable = ['title', 'platform', 'video_id', 'thumbnail_url', 'stream_date'];\n    protected function casts(): array\n    {\n        return [\n            'stream_date' => 'datetime',\n        ];\n    }\n}\n"
];

foreach ($models as $name => $content) {
    file_put_contents($dir . '/app/Models/' . $name . '.php', $content);
}

echo "Migrations and Models populated successfully!\n";
