<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomepageSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomepageSettingController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json(['data' => HomepageSetting::current()->content]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'hero_title' => ['sometimes', 'string', 'max:200'],
            'hero_subtitle' => ['sometimes', 'string', 'max:500'],
            'hero_cta_primary' => ['sometimes', 'string', 'max:100'],
            'hero_cta_secondary' => ['sometimes', 'string', 'max:100'],
            'jobs_section_title' => ['sometimes', 'string', 'max:200'],
            'jobs_section_subtitle' => ['sometimes', 'string', 'max:500'],
            'stats_section_title' => ['sometimes', 'string', 'max:200'],
            'stats_section_subtitle' => ['sometimes', 'string', 'max:500'],
            'how_it_works_title' => ['sometimes', 'string', 'max:200'],
            'how_it_works_subtitle' => ['sometimes', 'string', 'max:500'],
            'how_it_works_steps' => ['sometimes', 'array', 'max:6'],
            'how_it_works_steps.*.icon' => ['required_with:how_it_works_steps', 'string'],
            'how_it_works_steps.*.title' => ['required_with:how_it_works_steps', 'string', 'max:100'],
            'how_it_works_steps.*.description' => ['required_with:how_it_works_steps', 'string', 'max:300'],
            'apply_section_title' => ['sometimes', 'string', 'max:200'],
            'apply_section_subtitle' => ['sometimes', 'string', 'max:500'],
            'cta_title' => ['sometimes', 'string', 'max:200'],
            'cta_subtitle' => ['sometimes', 'string', 'max:500'],
            'cta_button' => ['sometimes', 'string', 'max:100'],
            'footer_tagline' => ['sometimes', 'string', 'max:300'],
            'footer_links' => ['sometimes', 'array', 'max:10'],
            'footer_links.*.label' => ['required_with:footer_links', 'string', 'max:80'],
            'footer_links.*.href' => ['required_with:footer_links', 'string', 'max:200'],
        ]);

        $setting = HomepageSetting::current();
        $setting->update(['content' => array_merge($setting->content, $validated)]);

        return response()->json(['data' => $setting->fresh()->content]);
    }
}
