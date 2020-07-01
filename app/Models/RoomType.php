<?php
/**
 * Model containing data regarding property room_type
 */

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RoomType
 */
class RoomType extends Model
{

    /**
     * Variable definition.
     *
     * @var string
     */
    protected $table = 'room_type';


    /**
     * Get all room types.
     *
     * @param integer $selected_room_type Selected Room Id.
     *
     * @return array
     */
    public static function getAllRoomTypes(int $selected_room_type=0)
    {
        // Propety types.
        $room_types = [];

        // Get room types linked to active properties.
        $room_types_query = self::select(
            'id',
            'name'
        );

        // Get room types.
        $room_types = $room_types_query->get()->toArray();

        // Property types.
        $response_room_types = [];
        foreach ($room_types as $room_type) {
            $response_room_types[] = [
                'id'       => $room_type['id'],
                'name'     => $room_type['name'],
                'selected' => ($room_type['id'] === $selected_room_type) ? 1 : 0,
            ];
        }

        return $response_room_types;

    }//end getAllRoomTypes()


}//end class
