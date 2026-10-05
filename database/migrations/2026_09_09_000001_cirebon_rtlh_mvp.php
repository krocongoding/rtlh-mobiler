<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->index();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['user_id', 'role_id']);
        });

        Schema::create('master_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('master_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_category_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('label');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['master_category_id', 'code']);
        });

        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('regions')->nullOnDelete();
            $table->string('code')->index();
            $table->string('name')->index();
            $table->string('type', 30)->index();
            $table->unsignedTinyInteger('level')->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('region_boundaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->string('source')->nullable();
            $table->string('version', 50)->nullable();
            $table->date('effective_date')->nullable();
            $table->timestamps();
        });
        DB::statement('ALTER TABLE region_boundaries ADD COLUMN geometry geometry(Geometry, 4326)');
        DB::statement('CREATE INDEX region_boundaries_geometry_gist ON region_boundaries USING GIST (geometry)');

        Schema::create('houses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->restrictOnDelete();
            $table->string('house_code', 50)->unique();
            $table->text('address')->nullable();
            $table->string('block', 100)->nullable();
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
            $table->decimal('area_m2', 10, 2)->nullable();
            $table->unsignedInteger('occupant_count')->nullable();
            $table->unsignedInteger('household_count')->nullable();
            $table->unsignedSmallInteger('survey_year')->index();
            $table->string('status', 30)->default('draft')->index();
            $table->boolean('is_public')->default(false)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
        DB::statement('ALTER TABLE houses ADD COLUMN location geometry(Point, 4326)');
        DB::statement('CREATE INDEX houses_location_gist ON houses USING GIST (location)');

        Schema::create('house_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->constrained()->cascadeOnDelete();
            $table->string('foundation')->nullable();
            $table->foreignId('sloof_condition_id')->nullable()->constrained('master_values')->nullOnDelete();
            $table->foreignId('column_condition_id')->nullable()->constrained('master_values')->nullOnDelete();
            $table->foreignId('beam_condition_id')->nullable()->constrained('master_values')->nullOnDelete();
            $table->timestamps();
        });
        Schema::create('house_floors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->nullable()->constrained('master_values')->nullOnDelete();
            $table->foreignId('condition_id')->nullable()->constrained('master_values')->nullOnDelete();
            $table->timestamps();
        });
        Schema::create('house_walls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->nullable()->constrained('master_values')->nullOnDelete();
            $table->foreignId('condition_id')->nullable()->constrained('master_values')->nullOnDelete();
            $table->timestamps();
        });
        Schema::create('house_ceilings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->constrained()->cascadeOnDelete();
            $table->foreignId('condition_id')->nullable()->constrained('master_values')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('house_roofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->constrained()->cascadeOnDelete();
            $table->foreignId('frame_condition_id')->nullable()->constrained('master_values')->nullOnDelete();
            $table->foreignId('material_id')->nullable()->constrained('master_values')->nullOnDelete();
            $table->foreignId('condition_id')->nullable()->constrained('master_values')->nullOnDelete();
            $table->timestamps();
        });
        Schema::create('house_sanitation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->constrained()->cascadeOnDelete();
            $table->foreignId('water_source_id')->nullable()->constrained('master_values')->nullOnDelete();
            $table->boolean('toilet_available')->nullable();
            $table->foreignId('toilet_type_id')->nullable()->constrained('master_values')->nullOnDelete();
            $table->foreignId('fecal_disposal_type_id')->nullable()->constrained('master_values')->nullOnDelete();
            $table->string('water_fecal_distance')->nullable();
            $table->timestamps();
        });
        Schema::create('house_utilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->constrained()->cascadeOnDelete();
            $table->string('light_opening', 30)->nullable();
            $table->string('ventilation', 30)->nullable();
            $table->foreignId('lighting_source_id')->nullable()->constrained('master_values')->nullOnDelete();
            $table->timestamps();
        });
        Schema::create('house_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->constrained()->cascadeOnDelete();
            $table->string('type', 50);
            $table->string('path');
            $table->string('disk', 50)->default('public');
            $table->string('caption')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
        Schema::create('house_occupants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->constrained()->cascadeOnDelete();
            $table->string('relationship', 50)->nullable();
            $table->string('gender', 20)->nullable();
            $table->unsignedSmallInteger('birth_year')->nullable();
            $table->string('occupation')->nullable();
            $table->string('education')->nullable();
            $table->boolean('is_primary_contact')->default(false);
            $table->timestamps();
        });

        Schema::create('rtlh_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('assessment_year');
            $table->date('assessment_date')->nullable();
            $table->foreignId('assessor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('draft')->index();
            $table->decimal('score', 8, 2)->nullable();
            $table->string('priority_level', 30)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['house_id', 'assessment_year']);
        });

        Schema::create('rtlh_assessment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('rtlh_assessments')->cascadeOnDelete();
            $table->string('category', 50);
            $table->string('item_code', 100);
            $table->string('value')->nullable();
            $table->decimal('score', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('rtlh_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedSmallInteger('year')->index();
            $table->foreignId('program_type_id')->nullable()->constrained('master_values')->nullOnDelete();
            $table->decimal('budget', 18, 2)->nullable();
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 30)->default('draft');
            $table->timestamps();
        });

        Schema::create('rtlh_program_houses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('rtlh_programs')->cascadeOnDelete();
            $table->foreignId('house_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30)->default('planned');
            $table->decimal('assistance_value', 18, 2)->nullable();
            $table->date('started_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['program_id', 'house_id']);
        });

        Schema::create('data_sources', function (Blueprint $table) {
            $table->id();
            $table->string('institution');
            $table->string('title');
            $table->text('url')->nullable();
            $table->date('publication_date')->nullable();
            $table->timestamp('accessed_at')->nullable();
            $table->text('methodology')->nullable();
            $table->text('license')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('indicators', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('unit')->nullable();
            $table->string('data_level', 30)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });

        Schema::create('indicator_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->decimal('value', 24, 6)->nullable();
            $table->foreignId('source_id')->nullable()->constrained('data_sources')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['indicator_id', 'region_id', 'year']);
        });

        Schema::create('datasets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->nullable()->constrained('data_sources')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('format', 30)->nullable();
            $table->string('access_type', 30)->default('public');
            $table->string('file_path')->nullable();
            $table->string('version', 50)->nullable();
            $table->date('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dataset_id')->nullable()->constrained()->nullOnDelete();
            $table->string('file_name');
            $table->string('file_type', 30)->nullable();
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('success_rows')->default(0);
            $table->unsignedInteger('failed_rows')->default(0);
            $table->string('status', 30)->default('pending');
            $table->timestamps();
        });

        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('dataset_id')->nullable()->constrained()->nullOnDelete();
            $table->string('format', 30);
            $table->json('filters')->nullable();
            $table->unsignedInteger('row_count')->nullable();
            $table->timestamp('downloaded_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50);
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['auditable_type', 'auditable_id']);
        });
    }

    public function down(): void
    {
        foreach ([
            'audit_logs','downloads','imports','datasets','indicator_values','indicators','data_sources',
            'rtlh_program_houses','rtlh_programs','rtlh_assessment_items','rtlh_assessments',
            'house_occupants','house_photos','house_utilities','house_sanitation','house_ceilings','house_roofs','house_walls','house_floors','house_structures','houses','region_boundaries','regions','master_values','master_categories','role_user','roles'
        ] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
