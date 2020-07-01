<?php
/**
 * PropertyServiceTest containing tests for PropertyService
 */

use App\Models\{Property, PropertyImage, CountryCodeMapping};
use App\Libraries\v1_6\{PropertyService};
use App\Libraries\Helper;
use Carbon\Carbon;

/**
 * Class PropertyServiceTest
 *
 * @group Services
 */
class PropertyServiceTest extends TestCase
{
    use App\Traits\FactoryHelper;


    /**
     * Setup.
     *
     * @return void
     */
    public function setup(): void
    {
        parent::setup();

        $this->mocked_property_model       = $this->mock(Property::class);
        $this->mocked_property_image_model = \Mockery::mock('overload:App\Models\PropertyImage');
        $this->property_service            = new PropertyService($this->mocked_property_model);

    }//end setup()


    /**
     * Tear down.
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $this->clearmock();

    }//end tearDown()


    /**
     * Test for Get prive manager properties method.
     *
     * @return void
     **/
    public function test_get_prive_manager_properties_method()
    {
        $prive_manager_id = 111111;
        $offset           = 10;
        $total            = 999;
        $active           = true;
        $property_id      = 9999;
        $headers          = [];

        $this->mocked_property_model->shouldReceive('getPriveManagerProperties')->with($prive_manager_id, $offset, $total, $active)->once()->andReturn(
            [
                [
                    'id'              => $property_id,
                    'country'         => 'IN',
                    'title'           => 'testing villa',
                    'area'            => 'Karol Bagh',
                    'city'            => 'New Delhi',
                    'state'           => 'Delhi',
                    'latitude'        => 28.652781,
                    'longitude'       => 77.192146,
                    'units'           => 10,
                    'per_night_price' => 1264.50,
                    'currency'        => 'INR',
                ],
            ]
        );

        $this->mocked_property_model->shouldReceive('getPriveMangerPropertyCounts')->with($prive_manager_id, $active)->once()->andReturn(1);

        $this->mocked_property_image_model->shouldReceive('getPropertiesImagesByIds')->with([$property_id], $headers, 1)->once()->andReturn([]);

        $mocked_country_code_mappings = \Mockery::mock('overload:App\Models\CountryCodeMapping');
        $mocked_country_code_mappings->shouldReceive('getCountries')->once()->andReturn(['IN' => ['name' => 'India', 'ccode' => 'IN']]);

        $properties_response = $this->property_service->getPriveManagerProperties($prive_manager_id, $headers, $offset, $total, $active);

        $this->assertEquals($properties_response['total_count'], 1);
        $this->assertEquals($properties_response['property_list'][0]['property_hash_id'], Helper::encodePropertyId($property_id));
        $this->assertEquals($properties_response['property_list'][0]['property_title'], $property_id.' • Testing villa');
        $this->assertEquals($properties_response['property_list'][0]['location']['area'], 'Karol Bagh');
        $this->assertEquals($properties_response['property_list'][0]['location']['city'], 'New Delhi');
        $this->assertEquals($properties_response['property_list'][0]['location']['state'], 'Delhi');
        $this->assertEquals($properties_response['property_list'][0]['location']['country'], ['name' => 'India', 'ccode' => 'IN']);
        $this->assertEquals($properties_response['property_list'][0]['location']['location_name'], Helper::formatLocation('Karol Bagh', 'New Delhi', 'Delhi'));
        $this->assertEquals($properties_response['property_list'][0]['location']['latitude'], 28.652781);
        $this->assertEquals($properties_response['property_list'][0]['location']['longitude'], 77.192146);
        $this->assertEquals($properties_response['property_list'][0]['units'], 10);
        $this->assertEquals($properties_response['property_list'][0]['per_night_price'], Helper::getFormattedMoney(1264.50, 'INR'));

    }//end test_get_prive_manager_properties_method()


}//end class
