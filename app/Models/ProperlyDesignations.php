<?php
/**
 * Model containing data regarding properly designations
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentQuery;
use DB;

/**
 * Class ProperlyDesignations
 */
class ProperlyDesignations extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'properly_designations';


    /**
     * Get Full tree of any node
     *
     * @param integer $designation_id Designation Id.
     *
     * @return array
     */
    public static function getFullTreeOfAnyNode(int $designation_id)
    {
        $sub_tree = self::select(
            'node.id',
            DB::raw('(COUNT(parent.id) -1) as depth')
        )->from('properly_designations as node')->join(
            'properly_designations as parent',
            function ($parent) use ($designation_id) {
                    $parent->whereBetween('node.lft', [DB::raw('parent.lft'), DB::raw('parent.rgt')])->where('node.id', $designation_id);
            }
        )->groupBy('node.name')->orderBy('node.lft');

        $node_tree = self::select(
            'node.id',
            'node.name',
            DB::raw('(COUNT(parent.id) - (sub_tree.depth + 1)) as depth')
        )->from('properly_designations as node')->join(
            'properly_designations as parent',
            function ($parent) {
                    $parent->whereBetween('node.lft', [DB::raw('parent.lft'), DB::raw('parent.rgt')]);
            }
        )->join(
            'properly_designations as sub_parent',
            function ($sub_parent) {
                        $sub_parent->whereBetween('node.lft', [DB::raw('sub_parent.lft'), DB::raw('sub_parent.rgt')]);
            }
        )->joinSub(
            $sub_tree,
            'sub_tree',
            function ($sub_tree_join) {
                        $sub_tree_join->where('sub_parent.id', DB::raw('sub_tree.id'));
            }
        )->groupBy('node.id')->orderBy('node.lft')->get();

        if (empty($node_tree) === true) {
            return [];
        }

        return $node_tree->toArray();

    }//end getFullTreeOfAnyNode()


    /**
     * Get single path
     *
     * @param integer $designation_id Designation Id.
     *
     * @return array
     */
    public static function getSinglePath(int $designation_id)
    {
        $node_tree = self::select(
            'parent.id',
            'parent.name'
        )->from('properly_designations as node')->join(
            'properly_designations as parent',
            function ($parent) use ($designation_id) {
                    $parent->whereBetween('node.lft', [DB::raw('parent.lft'), DB::raw('parent.rgt')])->where('node.id', $designation_id);
            }
        )->orderBy('parent.lft')->get();

        if (empty($node_tree) === true) {
            return [];
        }

        return $node_tree->toArray();

    }//end getSinglePath()


    /**
     * Get parent designation
     *
     * @param integer $designation_id Designation Id.
     *
     * @return array
     */
    public function getParentDesignation(int $designation_id)
    {
        $node_tree = self::select(
            'parent.id',
            'parent.name'
        )->from('properly_designations as node')->join(
            'properly_designations as parent',
            function ($parent) use ($designation_id) {
                    $parent->whereBetween('node.lft', [DB::raw('parent.lft'), DB::raw('parent.rgt')])->where('node.id', $designation_id);
            }
        )->orderBy('parent.lft', 'DESC')->offset(1)->limit(1)->get();

        if (empty($node_tree) === true) {
            return [];
        }

        return $node_tree->toArray();

    }//end getParentDesignation()


    /**
     * Get Search Link.
     *
     * @param string  $name      Designation name.
     * @param integer $parent_id Parent Designation Id.
     *
     * @return boolean
     */
    public static function addNode(string $name, int $parent_id)
    {
        $parent_detail = self::where('id', $parent_id)->first();

        if (empty($parent_detail) === true) {
            return false;
        }

        DB::raw('LOCK TABLE properly_designations WRITE');
        self::where('rgt', '>=', $parent_detail->rgt)->update(['rgt' => DB::raw('rgt + 2')]);
        self::where('lft', '>', $parent_detail->rgt)->update(['lft' => DB::raw('lft + 2')]);

        $new_node       = new self;
        $new_node->name = $name;
        $new_node->lft  = $parent_detail->rgt;
        $new_node->rgt  = ($parent_detail->rgt + 1);
        $new_node->save();

        DB::raw('UNLOCK TABLES');

        return true;

    }//end addNode()


    /**
     * Add node between two node (Parent and child).
     *
     * @param string  $name      Designation name.
     * @param integer $parent_id Parent Designation Id.
     * @param integer $node_id   Current Designation Id.
     *
     * @return boolean
     */
    public static function addMidNode(string $name, int $parent_id, int $node_id)
    {
        $parent_detail = self::where('id', $parent_id)->first();

        $current_node_detail = self::where('id', $node_id)->first();

        if (empty($parent_detail) === true || empty($current_node_detail) === true) {
            return false;
        }

        DB::raw('LOCK TABLE properly_designations WRITE');
        self::where('rgt', '>', $current_node_detail->rgt)->update(['rgt' => DB::raw('rgt + 2')]);
        self::where('lft', '>', $current_node_detail->rgt)->update(['lft' => DB::raw('lft + 2')]);
        self::whereBetween('lft', [$current_node_detail->lft, $current_node_detail->rgt])->update(['rgt' => DB::raw('rgt + 1'), 'lft' => DB::raw('lft + 1')]);

        $new_node       = new self;
        $new_node->name = $name;
        $new_node->lft  = $current_node_detail->lft;
        $new_node->rgt  = ($current_node_detail->rgt + 2);
        $new_node->save();

        $current_node_detail->lft = ($current_node_detail->lft + 1);
        $current_node_detail->rgt = ($current_node_detail->rgt + 1);
        $current_node_detail->save();

        DB::raw('UNLOCK TABLES');

        return true;

    }//end addMidNode()


}//end class
