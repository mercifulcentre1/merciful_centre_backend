<?php
$dir = __DIR__;

$migrationsData = [
    'admin_users' => <<<EOT
            \$table->id();
            \$table->string('username', 50)->unique();
            \$table->string('email')->unique();
            \$table->string('full_name');
            \$table->string('password_hash');
            \$table->enum('role', ['admin', 'super_admin'])->default('admin');
            \$table->dateTime('last_login')->nullable();
            \$table->timestamps();
EOT,
    'sermons' => <<<EOT
            \$table->id();
            \$table->string('title');
            \$table->string('preacher');
            \$table->date('date');
            \$table->text('description')->nullable();
            \$table->string('audio_url')->nullable();
            \$table->string('thumbnail_url')->nullable();
            \$table->timestamps();
EOT,
    'events' => <<<EOT
            \$table->id();
            \$table->string('title');
            \$table->dateTime('event_date');
            \$table->string('location')->nullable();
            \$table->text('description')->nullable();
            \$table->string('image_url')->nullable();
            \$table->timestamps();
EOT,
    'gallery' => <<<EOT
            \$table->id();
            \$table->string('title');
            \$table->text('description')->nullable();
            \$table->string('image_url');
            \$table->string('category', 50)->nullable();
            \$table->foreignId('uploaded_by')->nullable()->constrained('admin_users')->nullOnDelete();
            \$table->timestamps();
EOT,
    'settings' => <<<EOT
            \$table->string('setting_key', 50)->primary();
            \$table->text('setting_value')->nullable();
            \$table->timestamps();
EOT,
    'contact_messages' => <<<EOT
            \$table->id();
            \$table->string('name', 100);
            \$table->string('email', 100);
            \$table->string('subject');
            \$table->text('message');
            \$table->enum('status', ['new', 'read', 'replied'])->default('new');
            \$table->timestamps();
EOT,
    'livestream_settings' => <<<EOT
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
EOT,
    'livestream_archives' => <<<EOT
            \$table->id();
            \$table->string('title');
            \$table->enum('platform', ['youtube', 'facebook'])->default('youtube');
            \$table->string('video_id');
            \$table->string('thumbnail_url')->nullable();
            \$table->dateTime('stream_date');
            \$table->timestamps();
EOT
];

$migrations = glob($dir . '/database/migrations/*_create_*_table.php');
foreach ($migrations as $file) {
    $tableName = '';
    foreach (array_keys($migrationsData) as $name) {
        if (strpos($file, 'create_' . $name . '_table') !== false) {
            // handle galleries special case
            if ($name === 'galleries') {
                $tableName = 'gallery';
            } else {
                $tableName = $name;
            }
            $schema = $migrationsData[$name];
            break;
        }
    }
    
    // For gallery, the table was created as galleries.
    if (strpos($file, 'create_galleries_table') !== false) {
        $tableName = 'gallery';
        $schema = $migrationsData['gallery'];
    }

    if ($tableName) {
        $fullClass = <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
{$schema}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('{$tableName}');
    }
};
EOT;
        file_put_contents($file, $fullClass);
    }
}
echo "Migrations fixed!\n";
