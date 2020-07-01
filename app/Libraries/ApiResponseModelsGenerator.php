<?php
/**
 * ApiResponseModelsGenerator containing methods used to generate response file
 */

namespace App\Libraries;

use Illuminate\Support\Facades\File;
use Nette\PhpGenerator\{PhpFile, ClassType, GlobalFunction,    PhpNamespace};

/**
 * Class Helper
 */
class ApiResponseModelsGenerator
{

    /**
     * Space
     *
     * @var string
     */
    public static $space = ' ';


    /**
     * Generate Mappings File from Response
     *
     * @param string    $class_name Class name of response.
     * @param \StdClass $response   Response Data.
     * @param string    $version    Version.
     * @param string    $type       Type of mapping.
     *
     * @return string Mapping data.
     */
    public static function saveMappingsFromResponse(string $class_name, \StdClass $response, string $version, string $type='')
    {
        $mapping_data = self::getMappingsFromResponse($class_name, $response, $version, $type);

        if (empty($type) === true) {
             File::put(base_path().'/app/Http/Response/'.$version.'/Mappings/'.$class_name.'.json', json_encode($mapping_data, JSON_PRETTY_PRINT));
        } else {
             File::put(base_path().'/app/Http/Response/'.$version.'/Mappings/'.ucfirst($type).'/'.$class_name.'.json', json_encode($mapping_data, JSON_PRETTY_PRINT));
        }

        return $class_name.'.json Response Created successfully';

    }//end saveMappingsFromResponse()


    /**
     * Generate models from Mappings
     *
     * @param string $file_name File Name.
     * @param string $version   Version.
     * @param string $type      Type.
     *
     * @return string file name.
     */
    public static function getModelsFromMappings(string $file_name, string $version, string $type='')
    {
        if (empty($type) === true) {
            $json = File::get(base_path().'/app/Http/Response/'.$version.'/Mappings/'.$file_name.'.json');
        } else {
            $json = File::get(base_path().'/app/Http/Response/'.$version.'/Mappings/'.ucfirst($type).'/'.$file_name.'.json');
        }

        $decode_data  = json_decode($json, true);
        $reponse_code = self::getPhpCode($decode_data, $version, $type);
        if (empty($type) === true) {
            File::put(base_path().'/app/Http/Response/'.$version.'/Models/'.$file_name.'.php', $reponse_code);
        } else {
            File::put(base_path().'/app/Http/Response/'.$version.'/Models/'.ucfirst($type).'/'.$file_name.'.php', $reponse_code);
        }

        return $file_name.'.php Response Created successfully';

    }//end getModelsFromMappings()


    /**
     * Generate Mappings from Response
     *
     * @param string    $class_name Class name of response.
     * @param \StdClass $response   Response Data.
     * @param string    $version    Version.
     * @param string    $type       Type.
     *
     * @return array Mapping data.
     */
    public static function getMappingsFromResponse(string $class_name, \StdClass $response, string $version, string $type)
    {
        $mappings           = [];
        $mappings['import'] = ['App\Http\Response\\'.$version.'\\Models\ApiResponse'];

        if (empty($type) === true) {
            $mappings['namespace'] = 'App\Http\Response\\'.$version.'\\Models';
        } else {
            $mappings['namespace'] = 'App\Http\Response\\'.$version.'\\Models\\'.ucfirst($type);
            // $mappings['import']    = [
            // 'App\Http\Response\\'.$version.'\\Models\ApiResponse',
            // 'App\Http\Response\\'.$version.'\\Models\\'.ucfirst($type).'\\'.$class_name,
            // ];
        }

        $mappings['class']    = [
            'name'        => $class_name,
            'definition'  => $class_name,
            'description' => $class_name,
            'extend'      => 'App\Http\Response\\'.$version.'\\Models\ApiResponse',
        ];
        $mappings['variable'] = [];
        foreach ($response as $key => $value) {
            $child_mappings = [];

            $data_type = gettype($value);

            $default_values = self::getDefaultValues($data_type);

            $child_mappings = [
                'name'        => $key,
                'type'        => $default_values['type'],
                'default'     => $default_values['default_string'],
                'description' => 'Property '.ucwords(str_replace('_', ' ', $key)),
            ];

            if ($data_type === 'object') {
                $value = (array) $value;
            }

            if ($data_type === 'array' || $data_type === 'object') {
                $partial_class_name = str_replace(' ', '', ucwords(str_replace('_', ' ', ($key)))).'Response.json';

                if (file_exists(base_path().'/app/Http/Response/'.$version.'/Mappings/Partial/'.$partial_class_name) === false) {
                    $child_mappings['children'] = self::getChildrenMappingsFromResponse($value, $data_type);
                } else {
                     $mappings['import'] = [
                         'App\Http\Response\\'.$version.'\\Models\ApiResponse',
                         'App\Http\Response\\'.$version.'\\Models\\Partial\\'.str_replace(' ', '', ucwords(str_replace('_', ' ', ($key)))).'Response',
                     ];
                }
            }

            $mappings['variable'][] = $child_mappings;
        }//end foreach

        return $mappings;

    }//end getMappingsFromResponse()


    /**
     * Generate Children Mappings from Response
     *
     * @param array  $value     Child Mapping Value.
     * @param string $data_type Child data mapping type.
     *
     * @return array Collection Count.
     */
    // phpcs:disable
    public static function getChildrenMappingsFromResponse($response, $data_type)
    {
    // phpcs:enable
        if ((is_array($response) === true && array_diff_key($response, array_keys(array_keys($response)))) === false && isset($response[0]) === true) {
               $response = $response[0];
        }

        if (is_object($response) === true) {
               $response = (array) $response;
        }

        if (is_array($response) === false) {
               $response = ['value' => $response];
        }

        $mappings = [];

        $object_size = count($response);
        $counter     = 0;

        foreach ($response as $key => $values) {
            $counter++;

            $child_mappings = [];

            $type           = gettype($values);
            $default_values = self::getDefaultValues($type);

            if ($type === 'object') {
                $values = (array) $values;
            }

            $child_mappings = [
                'name'        => $key,
                'type'        => $default_values['type'],
                'description' => 'Property '.ucwords(str_replace('_', ' ', $key)),
                'default'     => $default_values['default_string'],
            ];

            if ($type === 'array' || $type === 'object') {
                $child_mappings['children'] = self::getChildrenMappingsFromResponse($values, $type);
            }

            $mappings[] = $child_mappings;
        }//end foreach

        return $mappings;

    }//end getChildrenMappingsFromResponse()


    /**
     * Get data for diffrent variable
     *
     * @param string $type Variable type.
     *
     * @return array data.
     */
    public static function getDefaultValues(string $type)
    {
        $defaults = [];
        switch (strtolower($type)) {
            case 'integer':
                    $values         = 'Integer';
                    $default        = 0;
                    $default_string = '0';
                    $type           = 'integer';
                    $class          = 'int';
            break;

            case 'string':
                    $values         = 'String';
                    $default        = '';
                    $default_string = '';
                    $type           = 'string';
                    $class          = 'string';
            break;

            case 'double':
            case 'float':
                    $values         = 'Float';
                    $default        = 0.0;
                    $default_string = '0.0';
                    $type           = 'float';
                    $class          = 'float';
            break;

            case 'array':
                    $values         = 'Array';
                    $default        = [];
                    $default_string = '[]';
                    $type           = 'array';
                    $class          = 'array';
            break;

            case 'object':
                    $values         = 'Object';
                    $default        = [];
                    $default_string = '{}';
                    $type           = 'object';
                    $class          = 'array';
            break;

            case 'boolean':
                    $values         = 'Boolean';
                    $default        = false;
                    $default_string = 'false';
                    $type           = 'boolean';
                    $class          = 'bool';
            break;

            case 'null':
                   $values         = 'Null';
                   $default        = '';
                   $default_string = '';
                   $type           = 'null';
                   $class          = '';
            break;

            default:
                $values         = 'None';
                $default        = 00;
                $default_string = '00';
                $type           = 'none';
                $class          = 'string';
            break;
        }//end switch

        $defaults['values']         = $values;
        $defaults['default']        = $default;
        $defaults['default_string'] = $default_string;
        $defaults['type']           = $type;
        $defaults['class']          = $class;

        return $defaults;

    }//end getDefaultValues()


    /**
     * Generate Code from Mappings
     *
     * @param array  $mappings Mapping.
     * @param string $version  Version.
     * @param string $type     Type.
     *
     * @return string Code.
     */
    public static function getPhpCode(array $mappings, string $version, string $type='')
    {
        $php_file = new PhpFile;
        $php_file->addComment($mappings['class']['description']);
        $namespace = $php_file->addNamespace($mappings['namespace']);

        foreach ($mappings['import'] as $value) {
            $namespace->addUse($value);
        }

        $class = $namespace->addClass($mappings['class']['name']);

        $class->addExtend($mappings['class']['extend'])->addComment('Class '.$mappings['class']['name']."\n")->addComment('// phpcs:disable')->addComment('@SWG\Definition(');
        $class->addComment('definition="'.$mappings['class']['definition'].'",')->addComment('description="'.$mappings['class']['description'].'",')->addComment(')')->addComment('// phpcs:enable');

        foreach ($mappings['variable'] as $values) {
                $get_default_value = self::getDefaultValues($values['type']);

                $depth_count = 1;
                $depth       = str_repeat(self::$space, (2 * $depth_count));

                $php_code_for_single_key = $class->addProperty($values['name'], $get_default_value['default'])->setVisibility('protected')->addComment($values['description']."\n");
                $php_code_for_single_key->addComment('@var '.strtolower($get_default_value['values']))->addComment('// phpcs:disable')->addComment('@SWG\Property(')->addComment($depth.'property="'.$values['name'].'",');
                $php_code_for_single_key->addComment($depth.'type="'.$get_default_value['type'].'",')->addComment($depth.'default="'.$get_default_value['default_string'].'",');

                $function_name = str_replace(' ', '', ucwords(str_replace('_', ' ', ($values['name']))));

            if ($values['type'] === 'object' || $values['type'] === 'array') {
                $class_name = str_replace(' ', '', ucwords(str_replace('_', ' ', ($values['name'])))).'Response.json';

                // Check If partial mapping exists.
                if (file_exists(base_path().'/app/Http/Response/'.$version.'/Mappings/Partial/'.$class_name) === true) {
                     $php_code_for_single_key->addComment('ref="#/definitions/'.str_replace('_', '', ucwords($values['name'], '_')).'Response",');

                     $php_code_for_single_key->addComment($depth.'description="'.$values['description'].'"');

                     $setter_method_body = '$response = new '.$function_name.'Response($'.$values['name'].');'."\n".' $response = $response->toArray();'."\n".' $this->'.$values['name'].'= $response;'."\n".'return $this;';
                } else {
                     $php_code_for_single_key->addComment($depth.'description="'.$values['description'].'",');

                     $php_code_for_single_key = self::getChildrenMappingCode($values['children'], $php_code_for_single_key, $depth_count, $values['type']);

                     $setter_method_body = '$this->'.$values['name'].'= $'.$values['name'].';'."\n".'return $this;';
                }
            } else {
                $php_code_for_single_key->addComment($depth.'description="'.$values['description'].'"');

                $setter_method_body = '$this->'.$values['name'].'= $'.$values['name'].';'."\n".'return $this;';
            }//end if

                $getter_method_body = 'return $this->'.$values['name'].';';
            if ($values['type'] === 'object') {
                $getter_method_body = 'return (empty($this->'.$values['name'].') === false) ? $this->'.$values['name'].' : new \stdClass;';
            }

                $php_code_for_single_key->addComment(')')->addComment('// phpcs:enable');

                   $class->addMethod('get'.$function_name)->addComment('Get '.ucfirst($values['name'])."\n")->addComment('@return '.$get_default_value['type'])->setBody($getter_method_body);

                   // phpcs:disable

                   $class->addMethod('set'.$function_name)->addComment('Set '.str_replace('_', ' ', ucfirst($values['name']))."\n")->addComment('@param '.$get_default_value['class'].' $'.$values['name']." ".str_replace('_', ' ', ucfirst($values['name'])).".\n")->addComment('@return self')->setBody($setter_method_body)->addParameter($values['name'])->setTypeHint($get_default_value['class']);;

                   // phpcs:enable
        }//end foreach

        return $php_file;

    }//end getPhpCode()


     /**
      * Generate Child models from Mappings
      *
      * @param array        $mappings        Data.
      * @param object $class       PHPGenerator object.
      * @param integer      $depth_count Space count.
      * @param string       $data_type   DataType.
      *
      * @return string file name.
      */
    	// phpcs:disable
    public static function getChildrenMappingCode(array $mappings, $class, int $depth_count, string $data_type)
    {
    	// phpcs:enable
        $object_size = count($mappings);
        $counter     = 0;

        $depth = str_repeat(self::$space, (2 * $depth_count));
        $depth_count++;
        $outer_depth = str_repeat(self::$space, (2 * ($depth_count)));

        if ($data_type === 'array') {
            $class->addComment($depth.'@SWG\Items(');
            $class->addComment($outer_depth.'type="object",');
        }

        foreach ($mappings as $values) {
            $counter++;
            $get_default_value = self::getDefaultValues($values['type']);
            $class->addComment($outer_depth.'@SWG\Property(');

            $depth_constant = str_repeat(self::$space, (2 * $depth_count));
            $depth_increase = str_repeat(self::$space, (2 * ($depth_count + 1)));
            $depth_decrease = str_repeat(self::$space, (2 * ($depth_count - 1)));

            $class->addComment($depth_increase.'property="'.$values['name'].'",')->addComment($depth_increase.'type="'.$get_default_value['type'].'",')->addComment($depth_increase.'default="'.$get_default_value['default_string'].'",');

            if ($values['type'] === 'object' || $values['type'] === 'array') {
                $class->addComment($depth_increase.'description="'.$values['description'].'",');
                $class = self::getChildrenMappingCode($values['children'], $class, ($depth_count + 1), $values['type']);
            } else {
                $class->addComment($depth_increase.'description="'.$values['description'].'"');
            }

            if ($object_size === 1 || $counter === $object_size) {
                $class->addComment($depth_constant.')');
            } else {
                $class->addComment($depth_constant.'),');
            }
        }//end foreach

        if ($data_type === 'array') {
               $class->addComment($depth.')');
        }

           return $class;

    }//end getChildrenMappingCode()


     /**
      * Generate Mappings from Response
      *
      * @param string    $class_name Class name of response.
      * @param \stdClass $response   Response Data.
      * @param string    $version    Version.
      * @param string    $type       Type.
      *
      * @return string Mapping data.
      */
    public static function getMappingsFromExistingMappings(string $class_name, \stdClass $response, string $version, string $type='')
    {
        if (empty($type) === true) {
             $json = File::get(base_path().'/app/Http/Response/'.$version.'/Mappings/'.$class_name.'.json');
        } else {
             $json = File::get(base_path().'/app/Http/Response/'.$version.'/Mappings/'.ucfirst($type).'/'.$class_name.'.json');
        }

        $existing_mappings = json_decode($json, true);

        $new_mapings_variables      = (self::getMappingsFromResponse($class_name, $response, $version, $type))['variable'];
        $existing_mapings_variables = $existing_mappings['variable'];
        $merged_mappings            = [
            'namespace' => $existing_mappings['namespace'],
            'import'    => $existing_mappings['import'],
            'class'     => $existing_mappings['class'],
            'variable'  => [],
        ];
        $self_object                = new self;
        $new_mapings_variables      = $self_object->makeKeyValuePair($new_mapings_variables);
        $existing_mapings_variables = $self_object->makeKeyValuePair($existing_mapings_variables);

        $merged_mappings['variable'] = $self_object->makeMergedMappingVeriables($new_mapings_variables, $existing_mapings_variables);

        if (empty($type) === true) {
            File::put(base_path().'/app/Http/Response/'.$version.'/Mappings/'.$class_name.'.json', json_encode($merged_mappings, JSON_PRETTY_PRINT));
        } else {
            File::put(base_path().'/app/Http/Response/'.$version.'/Mappings/'.ucfirst($type).'/'.$class_name.'.json', json_encode($merged_mappings, JSON_PRETTY_PRINT));
        }

        return $class_name.'.json Response Updated successfully';

    }//end getMappingsFromExistingMappings()


    /**
     * Make Key Value pair on basis of name
     *
     * @param array $data Data array.
     *
     * @return array data.
     */
    private function makeKeyValuePair(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_numeric($key) === true) {
                $data[$value['name']] = $value;
                if (isset($value['children']) === true) {
                    $self_object = new self;
                    $data[$value['name']]['children'] = $self_object->makeKeyValuePair($value['children']);
                }

                unset($data[$key]);
            } else {
                break;
            }
        }

        return $data;

    }//end makeKeyValuePair()


    /**
     * Remove key of array
     *
     * @param array $data Data array.
     *
     * @return array data.
     */
    private function removeKey(array $data)
    {
        $response = [];
        foreach ($data as $key => $value) {
            $child = $value;
            if (isset($value['children']) === true) {
                $self_object       = new self;
                $child['children'] = $self_object->removeKey($value['children']);
            }

            $response[] = $child;
        }

        return $response;

    }//end removeKey()


    /**
     * Merge mappings
     *
     * @param array $new_mapings_variables      New Mapping Variables.
     * @param array $existing_mapings_variables Existing Mapping Variables.
     *
     * @return array data.
     */
    private function makeMergedMappingVeriables(array $new_mapings_variables, array $existing_mapings_variables)
    {
        $self_object = new self;
        $response    = [];
        foreach ($new_mapings_variables as $key => $value) {
            if (array_key_exists($key, $existing_mapings_variables) === true) {
                $child_data = [
                    'name'        => $value['name'],
                    'type'        => $value['type'],
                    'description' => $existing_mapings_variables[$key]['description'],
                    'default'     => $value['default'],
                ];

                if (isset($value['children']) === true && isset($existing_mapings_variables[$key]['children']) === true) {
                    $child_data['children'] = $self_object->makeMergedMappingVeriables($value['children'], $existing_mapings_variables[$key]['children']);
                } else if (isset($value['children']) === true) {
                    $child_data['children'] = $self_object->removeKey($value['children']);
                }

                $response[] = $child_data;
            } else {
                $child_data = $value;

                if (isset($child_data['children']) === true) {
                    $child_data['children'] = $self_object->removeKey($child_data['children']);
                }

                $response[] = $child_data;
            }//end if
        }//end foreach

        return $response;

    }//end makeMergedMappingVeriables()


}//end class
