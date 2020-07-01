<?php
/**
 * Response Models for Booking Share Data On Facebook
 */

namespace App\Http\Response\v1_6\Models;

/**
 * Class GetBookingShareResponse
 *
 * // phpcs:disable
 * @SWG\Definition(
 * definition="GetBookingShareResponse",
 * description="Response Models for Booking Share Data On Facebook",
 * )
 * // phpcs:enable
 */
class GetBookingShareResponse extends ApiResponse
{

    /**
     * Content for Facebook Share
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="content",
	 *   type="string",
	 *   default="",
	 *   description="Content for Facebook Share"
	 * )
     * // phpcs:enable
     */
    protected $content = '';

    /**
     * Property Url
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="link",
	 *   type="string",
	 *   default="",
	 *   description="Property Url"
	 * )
     * // phpcs:enable
     */
    protected $link = '';

    /**
     * Property Share Image
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="share_image",
	 *   type="string",
	 *   default="",
	 *   description="Property Share Image"
	 * )
     * // phpcs:enable
     */
    protected $share_image = '';

    /**
     * Property Title
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_title",
	 *   type="string",
	 *   default="",
	 *   description="Property Title"
	 * )
     * // phpcs:enable
     */
    protected $property_title = '';

    /**
     * Property Desc
     *
     * @var string
	 * // phpcs:disable
	 * @SWG\Property(
	 *   property="property_desc",
	 *   type="string",
	 *   default="",
	 *   description="Property Desc"
	 * )
     * // phpcs:enable
     */
    protected $property_desc = '';


    /**
     * Get Content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;

    }//end getContent()


    /**
     * Set Content
     *
     * @param string $content Content.
     *
     * @return self
     */
    public function setContent(string $content)
    {
        $this->content = $content;
        return $this;

    }//end setContent()


    /**
     * Get Link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;

    }//end getLink()


    /**
     * Set Link
     *
     * @param string $link Link.
     *
     * @return self
     */
    public function setLink(string $link)
    {
        $this->link = $link;
        return $this;

    }//end setLink()


    /**
     * Get Share_image
     *
     * @return string
     */
    public function getShareImage()
    {
        return $this->share_image;

    }//end getShareImage()


    /**
     * Set Share image
     *
     * @param string $share_image Share image.
     *
     * @return self
     */
    public function setShareImage(string $share_image)
    {
        $this->share_image = $share_image;
        return $this;

    }//end setShareImage()


    /**
     * Get Property_title
     *
     * @return string
     */
    public function getPropertyTitle()
    {
        return $this->property_title;

    }//end getPropertyTitle()


    /**
     * Set Property title
     *
     * @param string $property_title Property title.
     *
     * @return self
     */
    public function setPropertyTitle(string $property_title)
    {
        $this->property_title = $property_title;
        return $this;

    }//end setPropertyTitle()


    /**
     * Get Property_desc
     *
     * @return string
     */
    public function getPropertyDesc()
    {
        return $this->property_desc;

    }//end getPropertyDesc()


    /**
     * Set Property desc
     *
     * @param string $property_desc Property desc.
     *
     * @return self
     */
    public function setPropertyDesc(string $property_desc)
    {
        $this->property_desc = $property_desc;
        return $this;

    }//end setPropertyDesc()


}//end class
