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
        // First, drop all description columns

        // Articles table - drop
        if (Schema::hasColumn('articles', 'description')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        // How we works table - drop
        if (Schema::hasColumn('how_we_works', 'description')) {
            Schema::table('how_we_works', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        // Customer rates table - drop
        if (Schema::hasColumn('customer_rates', 'description')) {
            Schema::table('customer_rates', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        // Why choose us table - drop
        if (Schema::hasColumn('why_choose_us', 'description')) {
            Schema::table('why_choose_us', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        // Services table - drop
        if (Schema::hasColumn('services', 'description')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
        if (Schema::hasColumn('services', 'short_description')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('short_description');
            });
        }

        // Videos table - drop
        if (Schema::hasColumn('videos', 'description')) {
            Schema::table('videos', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        // Success stories table - drop
        if (Schema::hasColumn('sucess_stories', 'description')) {
            Schema::table('sucess_stories', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        // Sliders table - drop
        if (Schema::hasColumn('sliders', 'description')) {
            Schema::table('sliders', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        // Constants table - drop
        if (Schema::hasColumn('constants', 'description')) {
            Schema::table('constants', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        // Now, add all description columns as text

        // Articles table - add (after 'title')
        Schema::table('articles', function (Blueprint $table) {
            $table->longText('description')->nullable()->after('title');
        });

        // How we works table - add (after 'title')
        Schema::table('how_we_works', function (Blueprint $table) {
            $table->longText('description')->nullable()->after('title');
        });

        // Customer rates table - add (after 'name')
        Schema::table('customer_rates', function (Blueprint $table) {
            $table->longText('description')->nullable()->after('name');
        });

        // Why choose us table - add (after 'title')
        Schema::table('why_choose_us', function (Blueprint $table) {
            $table->longText('description')->nullable()->after('title');
        });

        // Services table - add (description after 'title', short_description after 'description')
        Schema::table('services', function (Blueprint $table) {
            $table->longText('description')->nullable()->after('title');
            $table->longText('short_description')->nullable()->after('description');
        });

        // Videos table - add (after 'title')
        Schema::table('videos', function (Blueprint $table) {
            $table->longText('description')->nullable()->after('title');
        });

        // Success stories table - add (after 'rate')
        Schema::table('sucess_stories', function (Blueprint $table) {
            $table->longText('description')->nullable()->after('rate');
        });

        // Sliders table - add (after 'title')
        Schema::table('sliders', function (Blueprint $table) {
            $table->longText('description')->nullable()->after('title');
        });

        // Constants table - add (after 'name')
        Schema::table('constants', function (Blueprint $table) {
            $table->longText('description')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, drop all text description columns

        if (Schema::hasColumn('articles', 'description')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        if (Schema::hasColumn('how_we_works', 'description')) {
            Schema::table('how_we_works', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        if (Schema::hasColumn('customer_rates', 'description')) {
            Schema::table('customer_rates', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        if (Schema::hasColumn('why_choose_us', 'description')) {
            Schema::table('why_choose_us', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        if (Schema::hasColumn('services', 'description')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
        if (Schema::hasColumn('services', 'short_description')) {
            Schema::table('services', function (Blueprint $table) {
                $table->dropColumn('short_description');
            });
        }

        if (Schema::hasColumn('videos', 'description')) {
            Schema::table('videos', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        if (Schema::hasColumn('sucess_stories', 'description')) {
            Schema::table('sucess_stories', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        if (Schema::hasColumn('sliders', 'description')) {
            Schema::table('sliders', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        if (Schema::hasColumn('constants', 'description')) {
            Schema::table('constants', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        // Now, add back the original column types

        Schema::table('articles', function (Blueprint $table) {
            $table->longText('description')->nullable()->after('title');
        });

        Schema::table('how_we_works', function (Blueprint $table) {
            $table->json('description')->nullable()->after('title');
        });

        Schema::table('customer_rates', function (Blueprint $table) {
            $table->string('description')->nullable()->after('name');
        });

        Schema::table('why_choose_us', function (Blueprint $table) {
            $table->json('description')->nullable()->after('title');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->json('description')->nullable()->after('title');
            $table->json('short_description')->nullable()->after('description');
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->string('description')->nullable()->after('title');
        });

        Schema::table('sucess_stories', function (Blueprint $table) {
            $table->string('description')->nullable()->after('rate');
        });

        Schema::table('sliders', function (Blueprint $table) {
            $table->json('description')->nullable()->after('title');
        });

        Schema::table('constants', function (Blueprint $table) {
            $table->json('description')->nullable()->after('name');
        });
    }
};
