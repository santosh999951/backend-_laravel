<?php
/**
 * To generate multiple version swagger doc json files
 * This file contains GenerateDocsCommand that overrides Swagger GenerateDocsCommand functions
 */

namespace App\Console\Commands\Swagger;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use SwaggerLume\Generator;

/**
 * Class GenerateDocsCommand
 */
class GenerateDocsCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-doc:generate {version? : Version should be the directory name placed under Controllers directory, skip for all versions.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate docs';


    /**
     * Execute the console command.
     * This command will generate api document of requested version(s).
     *
     * @return void
     */
    public function handle()
    {
        try {
            $version = $this->argument('version');
            if (null === $version) {
                $api_controller_dirs = File::directories(CONTROLLER_PATH);

                // Get version numbers from basename of controller directories.
                $versions = array_map(
                    function ($value) {
                        return basename($value);
                    },
                    $api_controller_dirs
                );
            } else {
                if (false === File::exists(CONTROLLER_PATH.'/'.$version)
                    || false === File::exists(RESPONSE_PATH.'/'.$version)
                ) {
                    $this->error('Version \''.$version.'\' seems invalid, as directory by this version does not exist under controller or response directory.');
                    exit(0);
                }

                $versions = [$version];
            }//end if

            if (0 < count($versions)) {
                $this->generateAPIDocs($versions);
            }
        } catch (\Exception $ex) {
            $this->error($ex->getMessage());
        }//end try

    }//end handle()


    /**
     * Make main directory for api doc if not exists
     *
     * @return boolean
     */
    private function makeApiDirectoryIfNotExist()
    {
        // Create main doc directory if not exists for api doc file.
        $doc_main_dir = config('swagger-lume.paths.docs');
        if (false === File::exists($doc_main_dir)) {
            File::makeDirectory($doc_main_dir);
            $this->info('Created the missing main directory for API doc.');
        }

        return true;

    }//end makeApiDirectoryIfNotExist()


    /**
     * Get all version directories from controller and response directories
     *
     * @return array
     */
    private function getAllVersionDirectories()
    {
        return array_merge(File::directories(CONTROLLER_PATH), File::directories(RESPONSE_PATH));

    }//end getAllVersionDirectories()


    /**
     * Generate API doc for given versions
     *
     * @param array $versions Array of versions for which document to be generated.
     *
     * @return void
     */
    private function generateAPIDocs(array $versions)
    {
        $all_version_dirs = $this->getAllVersionDirectories();
        $this->makeApiDirectoryIfNotExist();

        foreach ($versions as $version) {
            $exclude_dirs = array_diff($all_version_dirs, [CONTROLLER_PATH.'/'.$version, RESPONSE_PATH.'/'.$version]);

            // Overrides swagger config.
            config(
                [
                    'swagger-lume.paths.excludes' => $exclude_dirs,
                    'swagger-lume.paths.docs'     => storage_path('api-docs').'/'.$version,
                ]
            );

            Generator::generateDocs();

            $this->info('Api doc '.$version.' (re-)generated successfully');
        }

    }//end generateAPIDocs()


}//end class
