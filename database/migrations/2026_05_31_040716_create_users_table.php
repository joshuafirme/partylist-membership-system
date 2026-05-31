<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Basic Auth & Profile
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('mobile_number')->unique()->nullable();
            $table->string('password')->nullable();

            // Dynamic RBAC (System Access Level)
            $table->foreignId('user_role_id')->nullable()->constrained('user_roles')->onDelete('set null');

            // Team Management (1 Primary Team)
            $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('set null');
            $table->enum('team_role', ['Team Leader', 'Coordinator', 'Member'])->nullable();

            // Member Information
            $table->enum('voter_status', ['registered', 'unregistered'])->default('unregistered');
            $table->date('birthday')->nullable();
            $table->string('sex', 10)->nullable();
            $table->string('address')->nullable();
            $table->string('barangay')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('precinct_no')->nullable();
            $table->string('occupation')->nullable();
            $table->string('profile_photo_path')->nullable();

            // e-ID and QR System
            $table->string('membership_number')->unique()->nullable();
            $table->string('qr_token')->unique()->nullable();

            // Status: 1 = active, 0 = inactive, 2 = suspended, 3 = expired
            $table->tinyInteger('status')->default(1)->comment('1=active, 0=inactive, 2=suspended, 3=expired');

            // Identity Trigger / Registration Source Tagging
            $table->string('registered_from')->default('web');
            $table->string('external_id')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};