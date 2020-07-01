<?php
/**
 * Model containing data regarding properly designation property mapping
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentQuery;
use DB;

/**
 * Class ProperlyDesignationPidMapping
 */
class ProperlyDesignationPidMapping extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'properly_designation_pid_mapping';


    /**
     * Get assgned properties.
     *
     * @param integer $designation_id Property Designation Id.
     * @param array   $properties     Property ids.
     *
     * @return void
     */
    public function assignProperty(int $designation_id, array $properties)
    {
        $property_mappings = $this->getDesignationProperties($designation_id);

        $existing_property = [];

        if (empty($property_mappings) === false) {
            $existing_property = array_column($property_mappings, 'property_id');
        }

        $non_existing_property = array_diff($properties, $existing_property);

        $parent_designation       = new ProperlyDesignations;
        $parent_mapped_properties = $this->getDesignationProperties(array_column($parent_designation->getParentDesignation($designation_id), 'id')[0]);

        $parent_properties = [];
        if (empty($parent_mapped_properties) === false) {
            $parent_properties = array_column($parent_mapped_properties, 'property_id');
        }

        $property_to_map = array_intersect($non_existing_property, $parent_properties);

        $bulk_insert_data = [];

        foreach ($property_to_map as $value) {
            $bulk_insert_data[] = [
                'property_id'    => $value,
                'designation_id' => $designation_id,
            ];
        }

        if (empty($bulk_insert_data) === false) {
            self::insert($bulk_insert_data);
        }

    }//end assignProperty()


    /**
     * Get Designation Properties.
     *
     * @param integer $designation_id Designation Id.
     *
     * @return array
     */
    public function getDesignationProperties(int $designation_id)
    {
        $properties = self::select('properly_designation_pid_mapping.property_id')->from('properly_designations as node')->join(
            'properly_designations as child',
            function ($child) {
                $child->whereBetween('child.lft', [DB::raw('node.lft'), DB::raw('node.rgt')]);
            }
        )->join(
            'properly_designation_pid_mapping',
            function ($properties) use ($designation_id) {
                    $properties->on('properly_designation_pid_mapping.designation_id', '=', 'child.id')->where('node.id', $designation_id);
            }
        )->distinct()->get();

        if (empty($properties) === true) {
            return [];
        }

        return $properties->toArray();

    }//end getDesignationProperties()


    /**
     * Update mappings.
     *
     * @param integer $designation_id     Designation Id.
     * @param integer $new_designation_id New Designation Id.
     * @param array   $properties         Properties.
     *
     * @return void
     */
    public static function updateMappings(int $designation_id, int $new_designation_id, array $properties)
    {
        self::where('designation_id', $designation_id)->whereIn('property_id', $properties)->update(['designation_id' => $new_designation_id]);

    }//end updateMappings()


}//end class
