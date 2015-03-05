# Uploading media files

For [uploading](https://dev.twitter.com/rest/public/uploading-media) media files is used different api, than for other edit/update calls.

This extension brings you methods which could handle all this stuff.

## Uploading

For successful upload user have to be authenticated and your app must have *write* permission. Upload is simple done with this call:

```php
class YourAppSomePresenter extends BasePresenter
{
	/**
	 * @var \IPub\Twitter\Client
	 */
	protected $twitter;

	public function actionUpload()
	{
		try {
			$mediaData = $this->twitter->uploadMedia('full/absolute/path/to/your/image.jpg');

		} catch (\IPub\OAuth\ApiException $ex) {
			// something went wrong
		}
	}
}
```

If upload is successful an media file details are returned, in other case an exception will be thrown.

> NOTE: when media file is uploaded with this method, don't forget to assign uploaded file to some status, other way it will be dropped.

## Uploading with status

If you want to post status with media file directly, it is easy, just put status text as second parameter and extension will do the trick.

```php
class YourAppSomePresenter extends BasePresenter
{
	/**
	 * @var \IPub\Twitter\Client
	 */
	protected $twitter;

	public function actionUpload()
	{
		try {
			$mediaData = $this->twitter->uploadMedia('full/absolute/path/to/your/image.jpg', 'Text of the status to be created....');

		} catch (\IPub\OAuth\ApiException $ex) {
			// something went wrong
		}
	}
}
```

> NOTE: For this type of media uploading is used different api method: **[statuses/update_with_media.json](https://dev.twitter.com/rest/reference/post/statuses/update_with_media)**