<?php

namespace JibayMcs\Nuwa\Commands;

use Illuminate\Console\Command;

class NuwaInstallCommand extends Command
{
    public $signature = 'nuwa:install {--seed : Seed default templates}';

    public $description = 'Install the Nuwa CMS plugin (publish config, run migrations, seed templates).';

    public function handle(): int
    {
        $this->components->info('Installing Nuwa CMS...');

        // Publish config
        $this->components->task('Publishing configuration', function () {
            $this->callSilently('vendor:publish', [
                '--tag' => 'nuwa-config',
            ]);
        });

        // Publish migrations
        $this->components->task('Publishing migrations', function () {
            $this->callSilently('vendor:publish', [
                '--tag' => 'nuwa-migrations',
            ]);
        });

        // Run migrations
        if ($this->confirm('Run migrations now?', true)) {
            $this->components->task('Running migrations', function () {
                $this->callSilently('migrate');
            });
        }

        // Seed default templates
        if ($this->option('seed') || $this->confirm('Seed default templates?', false)) {
            $this->components->task('Seeding default templates', function () {
                $this->seedDefaultTemplates();
            });
        }

        $this->components->info('Nuwa CMS installed successfully!');

        return self::SUCCESS;
    }

    protected function seedDefaultTemplates(): void
    {
        $templates = [
            [
                'name' => 'Blank',
                'slug' => 'blank',
                'description' => 'An empty page with no predefined blocks.',
                'category' => 'basic',
                'blocks' => [],
            ],
            [
                'name' => 'Landing Page',
                'slug' => 'landing-page',
                'description' => 'A landing page with hero, features, and call-to-action sections.',
                'category' => 'marketing',
                'blocks' => [
                    ['type' => 'hero', 'data' => ['title' => 'Welcome', 'subtitle' => 'Your landing page starts here.'], 'order' => 0],
                    ['type' => 'spacer', 'data' => ['height' => 40], 'order' => 1],
                    ['type' => 'text', 'data' => ['content' => '<h2>About</h2><p>Tell your visitors about your project.</p>'], 'order' => 2],
                    ['type' => 'spacer', 'data' => ['height' => 40], 'order' => 3],
                    ['type' => 'button', 'data' => ['label' => 'Get Started', 'url' => '#', 'style' => 'primary'], 'order' => 4],
                ],
            ],
            [
                'name' => 'About',
                'slug' => 'about',
                'description' => 'A simple about page.',
                'category' => 'basic',
                'blocks' => [
                    ['type' => 'text', 'data' => ['content' => '<h1>About Us</h1><p>Share your story here.</p>'], 'order' => 0],
                    ['type' => 'image', 'data' => ['alt' => 'About image'], 'order' => 1],
                ],
            ],
            [
                'name' => 'Contact',
                'slug' => 'contact',
                'description' => 'A contact page.',
                'category' => 'basic',
                'blocks' => [
                    ['type' => 'text', 'data' => ['content' => '<h1>Contact Us</h1><p>Get in touch with us.</p>'], 'order' => 0],
                ],
            ],
        ];

        $model = \JibayMcs\Nuwa\Models\Template::class;

        foreach ($templates as $template) {
            $model::updateOrCreate(
                ['slug' => $template['slug']],
                $template,
            );
        }
    }
}
