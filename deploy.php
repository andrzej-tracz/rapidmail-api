<?php
namespace Deployer;

require 'recipe/symfony3.php';

// Project name
set('application', 'RapidMailAPI');
set('keep_releases', 3);
set('repository', 'git@gitlab.com:andrzejtracz/rapidmail-api.git');
set('git_tty', false);

// Shared files/dirs between deploys 
add('shared_files', [
    '.env'
]);

add('shared_dirs', [
    'var/sessions',
    'var/log',
    'var/storage',
    'vendor',
    'public/errors',
    'public/assets',
    'public/media',
]);

add('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts
host('s17.mydevil.net')
    ->stage('dev')
    ->set('branch', 'master')
    ->set('keep_releases', 1)
    ->user('andrzej-tracz')
    ->set('bin/php', '/usr/local/bin/php72')
    ->set('bin/npm', 'npm8')
    ->set('deploy_path', '~/domains/rapidmail.at-dev.ovh/deployer');

/**
 * Migrate database
 */
task('database:migrate', function () {
    run('{{bin/php}} {{bin/console}} doctrine:migrations:migrate {{console_options}} --allow-no-migration');
})->desc('Migrate database');

/**
 * Install assets from public dir of bundles
 */
task('deploy:assets:install', function () {
    run('{{bin/php}} {{bin/console}} assets:install {{console_options}} {{release_path}}/public');
})->desc('Install bundle assets');

/**
 * Restart process runing in background
 */
task('deploy:pm2:restart', function () {
    run('cd {{current_path}} && ~/node_modules/.bin/pm2 restart');
})->desc('Install bundle assets');

/**
 * Main task
 */
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:clear_paths',
    'deploy:create_cache_dir',
    'deploy:shared',
    'deploy:assets',
    'deploy:vendors',
    'deploy:assets:install',
    'deploy:assetic:dump',
    'deploy:cache:clear',
    'deploy:cache:warmup',
    'database:migrate',
    'deploy:symlink',
    'deploy:pm2:restart',
    'deploy:unlock',
    'cleanup',
])->desc('Deploy your project');

// Display success message on completion
after('deploy', 'success');

after('deploy:failed', 'deploy:unlock');
