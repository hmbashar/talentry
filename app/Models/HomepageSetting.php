<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSetting extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return [
            'hero_title' => 'Find Your Dream Job Today',
            'hero_subtitle' => 'Connecting top talent with world-class companies. Discover thousands of handpicked opportunities tailored to your skills.',
            'hero_cta_primary' => 'Browse All Jobs',
            'hero_cta_secondary' => 'For Employers',
            'jobs_section_title' => 'Latest Opportunities',
            'jobs_section_subtitle' => 'Hand-picked roles from leading companies, updated daily.',
            'stats_section_title' => 'Trusted by Thousands',
            'stats_section_subtitle' => 'Real numbers. Real opportunities.',
            'how_it_works_title' => 'How It Works',
            'how_it_works_subtitle' => 'Three simple steps to land your next role',
            'how_it_works_steps' => [
                ['icon' => 'UserPlus', 'title' => 'Create Your Profile', 'description' => 'Sign up in minutes and build a professional profile that stands out to employers.'],
                ['icon' => 'Search', 'title' => 'Explore Opportunities', 'description' => 'Browse curated job listings matched to your skills, experience and preferences.'],
                ['icon' => 'CheckCircle', 'title' => 'Apply & Get Hired', 'description' => 'Apply with one click and land interviews with world-class companies.'],
            ],
            'apply_section_title' => 'Jump-Start Your Career',
            'apply_section_subtitle' => 'Submit a quick application and let top employers discover you.',
            'cta_title' => 'Ready to Take the Next Step?',
            'cta_subtitle' => 'Join thousands of professionals who found their dream jobs through Talentry.',
            'cta_button' => 'Get Started Free',
            'footer_tagline' => 'Connecting talent with opportunity, one job at a time.',
            'footer_links' => [
                ['label' => 'Browse Jobs', 'href' => '/careers'],
                ['label' => 'About Us', 'href' => '#'],
                ['label' => 'Privacy Policy', 'href' => '#'],
                ['label' => 'Terms of Service', 'href' => '#'],
            ],
        ];
    }

    public static function current(): self
    {
        return static::firstOrCreate([], ['content' => static::defaults()]);
    }
}
