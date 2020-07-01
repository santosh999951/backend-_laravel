<?php

declare(strict_types=1);

namespace PackageVersions;

/**
 * This class is generated by ocramius/package-versions, specifically by
 * @see \PackageVersions\Installer
 *
 * This file is overwritten at every run of `composer install` or `composer update`.
 */
final class Versions
{
    public const ROOT_PACKAGE_NAME = 'guesthouser/guesthouser-api';
    /**
     * Array of all available composer packages.
     * Dont read this array from your calling code, but use the \PackageVersions\Versions::getVersion() method instead.
     *
     * @var array<string, string>
     * @internal
     */
    public const VERSIONS          = array (
  'aws/aws-sdk-php' => '3.120.0@289d716c7a418fc30a530ca83107e738f8dd1ebc',
  'aws/aws-sdk-php-laravel' => '3.4.0@90f72efd7ec85c1141b397079112f58e0ca0143b',
  'barryvdh/laravel-dompdf' => 'v0.8.5@7393732b2f3a3ee357974cbb0c46c9b65b84dad1',
  'brianium/habitat' => 'v1.0.0@d0979e3bb379cbc78ecb42b3ac171bc2b7e06d96',
  'brianium/paratest' => 'dev-2.2.0_gh@5558c6a9a44a208aef37a95fdf5a98bbef148915',
  'clue/stream-filter' => 'v1.4.1@5a58cc30a8bd6a4eb8f856adf61dd3e013f53f71',
  'composer/ca-bundle' => '1.2.4@10bb96592168a0f8e8f6dcde3532d9fa50b0b527',
  'composer/semver' => '1.5.0@46d9139568ccb8d9e7cdd4539cab7347568a5e2e',
  'darkaonline/swagger-lume' => '5.5.3@5bf281ce7be380bcf0f9a2dd257efae67f5aebe7',
  'defuse/php-encryption' => 'v2.2.1@0f407c43b953d571421e0020ba92082ed5fb7620',
  'doctrine/annotations' => 'v1.8.0@904dca4eb10715b92569fbcd79e201d5c349b6bc',
  'doctrine/inflector' => '1.3.1@ec3a55242203ffa6a4b27c58176da97ff0a7aec1',
  'doctrine/instantiator' => '1.3.0@ae466f726242e637cebdd526a7d991b9433bacf1',
  'doctrine/lexer' => '1.2.0@5242d66dbeb21a30dd8a3e66bf7a73b66e05e1f6',
  'dompdf/dompdf' => 'v0.8.3@75f13c700009be21a1965dc2c5b68a8708c22ba2',
  'dragonmantank/cron-expression' => 'v2.3.0@72b6fbf76adb3cf5bc0db68559b33d41219aba27',
  'dusterio/lumen-passport' => 'dev-gh-master-update2@b1a3ffd8b96df8cb9db4fc973bd2fccacccfbfbc',
  'egulias/email-validator' => '2.1.11@92dd169c32f6f55ba570c309d83f5209cefb5e23',
  'elasticsearch/elasticsearch' => 'v7.0.0@e8d035bac967aeb6a47aca1f9b07a25e01cda93f',
  'erusev/parsedown' => '1.7.3@6d893938171a817f4e9bc9e86f2da1e370b7bcd7',
  'firebase/php-jwt' => 'v5.0.0@9984a4d3a32ae7673d6971ea00bae9d0a1abba0e',
  'fzaninotto/faker' => 'v1.9.0@27a216cbe72327b2d6369fab721a5843be71e57d',
  'geoip2/geoip2' => 'v2.9.0@a807fbf65212eef5d8d2db1a1b31082b53633d77',
  'google/apiclient' => 'v2.4.0@cd3c37998020d91ae4eafca4f26a92da4dabba83',
  'google/apiclient-services' => 'v0.121@a33fd9ed19fe4e27f2ccebbf45646f38e7cb95af',
  'google/auth' => 'v1.6.1@45635ac69d0b95f38885531d4ebcdfcb2ebb6f36',
  'guzzlehttp/guzzle' => '6.4.1@0895c932405407fd3a7368b6910c09a24d26db11',
  'guzzlehttp/promises' => 'v1.3.1@a59da6cf61d80060647ff4d3eb2c03a2bc694646',
  'guzzlehttp/psr7' => '1.6.1@239400de7a173fe9901b9ac7c06497751f00727a',
  'guzzlehttp/ringphp' => '1.1.1@5e2a174052995663dd68e6b5ad838afd47dd615b',
  'guzzlehttp/streams' => '3.0.0@47aaa48e27dae43d39fc1cea0ccf0d84ac1a2ba5',
  'hashids/hashids' => '2.0.4@7a945a5192d4a5c8888364970feece9bc26179df',
  'http-interop/http-factory-guzzle' => '1.0.0@34861658efb9899a6618cef03de46e2a52c80fc0',
  'illuminate/auth' => 'v5.8.35@59d63d9dfda2836e8a75b4f1c6df8e2be3fb3909',
  'illuminate/broadcasting' => 'v5.8.35@b1217ccf631e86ed17d59cdd43562555996e9a48',
  'illuminate/bus' => 'v5.8.35@6a15b03cdc6739c3f2898d67dc4fe21357d60e07',
  'illuminate/cache' => 'v5.8.35@e6acac59f94c6362809b580918f7f3f6142d5796',
  'illuminate/config' => 'v5.8.35@6dac1dee3fb51704767c69a07aead1bc75c12368',
  'illuminate/console' => 'v5.8.35@e6e4708e6c6baaf92120848e885855ab3d76f30f',
  'illuminate/container' => 'v5.8.35@b42e5ef939144b77f78130918da0ce2d9ee16574',
  'illuminate/contracts' => 'v5.8.35@00fc6afee788fa07c311b0650ad276585f8aef96',
  'illuminate/database' => 'v5.8.35@56635c5e683a2e3c6c01a8a3bcad3683223d3cec',
  'illuminate/encryption' => 'v5.8.35@135c631bab0e0a8b9535b5750687e0a867c85193',
  'illuminate/events' => 'v5.8.35@a85d7c273bc4e3357000c5fc4812374598515de3',
  'illuminate/filesystem' => 'v5.8.35@494ba903402d64ec49c8d869ab61791db34b2288',
  'illuminate/hashing' => 'v5.8.35@56a9f294d9615bbbb14e2093fb0537388952cc2c',
  'illuminate/http' => 'v5.8.35@cd0f549611de16b323af88478b441e4d52ceef40',
  'illuminate/log' => 'v5.8.35@1d23931e0ff74fa461fc44dc1594c66f8f6ad36b',
  'illuminate/mail' => 'v5.8.35@30a624e0273d5e551a193dca204ce6abdfbbfee8',
  'illuminate/pagination' => 'v5.8.35@391134bc87a47b3dfe5cf60df73e5e0080aec220',
  'illuminate/pipeline' => 'v5.8.35@9e81b335d853ddd633a86a7f7e3fceed3b14f3d7',
  'illuminate/queue' => 'v5.8.35@36559f77916c16643bc614765db1e840d7bd9a00',
  'illuminate/redis' => 'v5.8.35@59f47da5c12a5d808582b2c1c74b2912a4e2176e',
  'illuminate/session' => 'v5.8.35@087d360f7b9d75bc964280b890c2f2fe8efaf71f',
  'illuminate/support' => 'v5.8.35@e63a495d3bf01654f70def1046fb925c4bb56506',
  'illuminate/translation' => 'v5.8.35@a23986a9ae77013046426bbeb4fe9a29e2527f76',
  'illuminate/validation' => 'v5.8.35@dec713980d95b41e2ce915e1d6d844a969321261',
  'illuminate/view' => 'v5.8.35@c859919bc3be97a3f114377d5d812f047b8ea90d',
  'intervention/image' => '2.5.1@abbf18d5ab8367f96b3205ca3c89fb2fa598c69e',
  'jaybizzle/crawler-detect' => 'v1.2.89@374d699ce4944107015eee0798eab072e3c47df9',
  'jean85/pretty-package-versions' => '1.2@75c7effcf3f77501d0e0caa75111aff4daa0dd48',
  'jenssegers/agent' => 'v2.6.3@bcb895395e460478e101f41cdab139c48dc721ce',
  'laravel/lumen-framework' => 'v5.8.13@5d1d1ba8dbc5b69a17370d276e96d30eb00a2b48',
  'laravel/passport' => 'dev-gh-master-update2@ae1919a783c04768114c5db5d877ca5efeb4719d',
  'lcobucci/jwt' => '3.3.1@a11ec5f4b4d75d1fcd04e133dede4c317aac9e18',
  'league/event' => '2.2.0@d2cc124cf9a3fab2bb4ff963307f60361ce4d119',
  'league/flysystem' => '1.0.57@0e9db7f0b96b9f12dcf6f65bc34b72b1a30ea55a',
  'league/oauth2-server' => 'dev-gh-master-update2@f866c978891411f1e5dc1a02b91c4300fb2bc688',
  'maxmind-db/reader' => 'v1.5.0@bd436094fc0a9b0558a899fb80b0ae34fe1808a0',
  'maxmind/web-service-common' => 'v0.5.0@61a9836fa3bb1743ab89752bae5005d71e78c73b',
  'mobiledetect/mobiledetectlib' => '2.8.34@6f8113f57a508494ca36acbcfa2dc2d923c7ed5b',
  'monolog/monolog' => '1.25.2@d5e2fb341cb44f7e2ab639d12a1e5901091ec287',
  'mtdowling/jmespath.php' => '2.4.0@adcc9531682cf87dfda21e1fd5d0e7a41d292fac',
  'myclabs/deep-copy' => '1.9.3@007c053ae6f31bba39dfa19a7726f56e9763bbea',
  'nesbot/carbon' => '2.27.0@13b8485a8690f103bf19cba64879c218b102b726',
  'nette/php-generator' => 'v3.2.3@aea6e81437bb238e5f0e5b5ce06337433908e63b',
  'nette/utils' => 'v3.0.2@c133e18c922dcf3ad07673077d92d92cef25a148',
  'nikic/fast-route' => 'v1.3.0@181d480e08d9476e61381e04a71b34dc0432e812',
  'ocramius/package-versions' => '1.5.1@1d32342b8c1eb27353c8887c366147b4c2da673c',
  'opis/closure' => '3.4.1@e79f851749c3caa836d7ccc01ede5828feb762c7',
  'paragonie/random_compat' => 'v9.99.99@84b4dfb120c6f9b4ff7b3685f9b8f1aa365a0c95',
  'phar-io/manifest' => '1.0.3@7761fcacf03b4d4f16e7ccb606d4879ca431fcf4',
  'phar-io/version' => '2.0.1@45a2ec53a73c70ce41d55cedef9063630abaf1b6',
  'phenx/php-font-lib' => '0.5.1@760148820110a1ae0936e5cc35851e25a938bc97',
  'phenx/php-svg-lib' => 'v0.3.3@5fa61b65e612ce1ae15f69b3d223cb14ecc60e32',
  'php-http/client-common' => '2.1.0@a8b29678d61556f45d6236b1667db16d998ceec5',
  'php-http/curl-client' => '2.0.0@e7a2a5ebcce1ff7d75eaf02b7c85634a6fac00da',
  'php-http/discovery' => '1.7.0@e822f86a6983790aa17ab13aa7e69631e86806b6',
  'php-http/httplug' => 'v2.0.0@b3842537338c949f2469557ef4ad4bdc47b58603',
  'php-http/message' => '1.8.0@ce8f43ac1e294b54aabf5808515c3554a19c1e1c',
  'php-http/message-factory' => 'v1.0.2@a478cb11f66a6ac48d8954216cfed9aa06a501a1',
  'php-http/promise' => 'v1.0.0@dc494cdc9d7160b9a09bd5573272195242ce7980',
  'phpdocumentor/reflection-common' => '2.0.0@63a995caa1ca9e5590304cd845c15ad6d482a62a',
  'phpdocumentor/reflection-docblock' => '4.3.2@b83ff7cfcfee7827e1e78b637a5904fe6a96698e',
  'phpdocumentor/type-resolver' => '1.0.1@2e32a6d48972b2c1976ed5d8967145b6cec4a4a9',
  'phpoption/phpoption' => '1.5.2@2ba2586380f8d2b44ad1b9feb61c371020b27793',
  'phpseclib/phpseclib' => '2.0.23@c78eb5058d5bb1a183133c36d4ba5b6675dfa099',
  'phpspec/prophecy' => '1.9.0@f6811d96d97bdf400077a0cc100ae56aa32b9203',
  'phpunit/php-code-coverage' => '6.1.4@807e6013b00af69b6c5d9ceb4282d0393dbb9d8d',
  'phpunit/php-file-iterator' => '2.0.2@050bedf145a257b1ff02746c31894800e5122946',
  'phpunit/php-text-template' => '1.2.1@31f8b717e51d9a2afca6c9f046f5d69fc27c8686',
  'phpunit/php-timer' => '2.1.2@1038454804406b0b5f5f520358e78c1c2f71501e',
  'phpunit/php-token-stream' => '3.1.1@995192df77f63a59e47f025390d2d1fdf8f425ff',
  'phpunit/phpunit' => '7.5.17@4c92a15296e58191a4cd74cff3b34fc8e374174a',
  'predis/predis' => 'v1.1.1@f0210e38881631afeafb56ab43405a92cafd9fd1',
  'psr/cache' => '1.0.1@d11b50ad223250cf17b86e38383413f5a6764bf8',
  'psr/container' => '1.0.0@b7ce3b176482dbbc1245ebf52b181af44c2cf55f',
  'psr/http-client' => '1.0.0@496a823ef742b632934724bf769560c2a5c7c44e',
  'psr/http-factory' => '1.0.1@12ac7fcd07e5b077433f5f2bee95b3a771bf61be',
  'psr/http-message' => '1.0.1@f6561bf28d520154e4b0ec72be95418abe6d9363',
  'psr/log' => '1.1.2@446d54b4cb6bf489fc9d75f55843658e6f25d801',
  'psr/simple-cache' => '1.0.1@408d5eafb83c57f6365a3ca330ff23aa4a5fa39b',
  'ralouphie/getallheaders' => '3.0.3@120b605dfeb996808c31b6477290a714d356e822',
  'ramsey/uuid' => '3.8.0@d09ea80159c1929d75b3f9c60504d613aeb4a1e3',
  'razorpay/razorpay' => '2.5.0@96c0167176cf53e3da15640622e9b993a3450ec7',
  'react/promise' => 'v2.7.1@31ffa96f8d2ed0341a57848cbb84d88b89dd664d',
  'rmccue/requests' => 'v1.7.0@87932f52ffad70504d93f04f15690cf16a089546',
  'sabberworm/php-css-parser' => '8.3.0@91bcc3e3fdb7386c9a2e0e0aa09ca75cc43f121f',
  'sebastian/code-unit-reverse-lookup' => '1.0.1@4419fcdb5eabb9caa61a27c7a1db532a6b55dd18',
  'sebastian/comparator' => '3.0.2@5de4fc177adf9bce8df98d8d141a7559d7ccf6da',
  'sebastian/diff' => '3.0.2@720fcc7e9b5cf384ea68d9d930d480907a0c1a29',
  'sebastian/environment' => '4.2.3@464c90d7bdf5ad4e8a6aea15c091fec0603d4368',
  'sebastian/exporter' => '3.1.2@68609e1261d215ea5b21b7987539cbfbe156ec3e',
  'sebastian/global-state' => '2.0.0@e8ba02eed7bbbb9e59e43dedd3dddeff4a56b0c4',
  'sebastian/object-enumerator' => '3.0.3@7cfd9e65d11ffb5af41198476395774d4c8a84c5',
  'sebastian/object-reflector' => '1.1.1@773f97c67f28de00d397be301821b06708fca0be',
  'sebastian/recursion-context' => '3.0.0@5b0cd723502bac3b006cbf3dbf7a1e3fcefe4fa8',
  'sebastian/resource-operations' => '2.0.1@4d7a795d35b889bf80a0cc04e08d77cedfa917a9',
  'sebastian/version' => '2.0.1@99732be0ddb3361e16ad77b68ba41efc8e979019',
  'sentry/sdk' => '2.0.4@4c115873c86ad5bd0ac6d962db70ca53bf8fb874',
  'sentry/sentry' => '2.2.4@a74999536b9119257cb1a4b1aa038e4a08439f67',
  'sentry/sentry-laravel' => '1.1.0@1e5f644b62ee73424ad8316e37b9a2dcf7290de8',
  'spatie/laravel-permission' => '3.2.0@e3821559c69b2b1ea8fdd967fa89ba13a01ead78',
  'spatie/laravel-tail' => '3.3.0@643888e3c5a3fe3a88b172c06d3a837ae33de93b',
  'squizlabs/php_codesniffer' => '3.5.2@65b12cdeaaa6cd276d4c3033a95b9b88b12701e7',
  'swagger-api/swagger-ui' => 'v3.24.3@94e101924b84435585896f4ea7c4092182a91f23',
  'swiftmailer/swiftmailer' => 'v6.2.3@149cfdf118b169f7840bbe3ef0d4bc795d1780c9',
  'symfony/console' => 'v4.4.0@35d9077f495c6d184d9930f7a7ecbd1ad13c7ab8',
  'symfony/css-selector' => 'v5.0.0@19d29e7098b7b2c3313cb03902ca30f100dcb837',
  'symfony/debug' => 'v4.4.0@b24b791f817116b29e52a63e8544884cf9a40757',
  'symfony/error-handler' => 'v4.4.0@e1acb58dc6a8722617fe56565f742bcf7e8744bf',
  'symfony/event-dispatcher' => 'v4.4.0@ab1c43e17fff802bef0a898f3bc088ac33b8e0e1',
  'symfony/event-dispatcher-contracts' => 'v1.1.7@c43ab685673fb6c8d84220c77897b1d6cdbe1d18',
  'symfony/finder' => 'v4.4.0@ce8743441da64c41e2a667b8eb66070444ed911e',
  'symfony/http-foundation' => 'v4.4.0@502040dd2b0cf0a292defeb6145f4d7a4753c99c',
  'symfony/http-kernel' => 'v4.4.0@5a5e7237d928aa98ff8952050cbbf0135899b6b0',
  'symfony/mime' => 'v5.0.0@76f3c09b7382bf979af7bcd8e6f8033f1324285e',
  'symfony/options-resolver' => 'v4.4.0@2be23e63f33de16b49294ea6581f462932a77e2f',
  'symfony/polyfill-ctype' => 'v1.12.0@550ebaac289296ce228a706d0867afc34687e3f4',
  'symfony/polyfill-iconv' => 'v1.12.0@685968b11e61a347c18bf25db32effa478be610f',
  'symfony/polyfill-intl-idn' => 'v1.12.0@6af626ae6fa37d396dc90a399c0ff08e5cfc45b2',
  'symfony/polyfill-mbstring' => 'v1.12.0@b42a2f66e8f1b15ccf25652c3424265923eb4f17',
  'symfony/polyfill-php72' => 'v1.12.0@04ce3335667451138df4307d6a9b61565560199e',
  'symfony/polyfill-php73' => 'v1.12.0@2ceb49eaccb9352bff54d22570276bb75ba4a188',
  'symfony/process' => 'v4.4.0@75ad33d9b6f25325ebc396d68ad86fd74bcfbb06',
  'symfony/psr-http-message-bridge' => 'v1.2.0@9ab9d71f97d5c7d35a121a7fb69f74fee95cd0ad',
  'symfony/service-contracts' => 'v2.0.0@9d99e1556417bf227a62e14856d630672bf10eaf',
  'symfony/translation' => 'v4.4.0@897fb68ee7933372517b551d6f08c6d4bb0b8c40',
  'symfony/translation-contracts' => 'v2.0.0@8feb81e6bb1a42d6a3b1429c751d291eb6d05297',
  'symfony/var-dumper' => 'v4.4.0@eade2890f8b0eeb279b6cf41b50a10007294490f',
  'theseer/tokenizer' => '1.1.3@11336f6f84e16a720dae9d8e6ed5019efa85a0f9',
  'tijsverkoyen/css-to-inline-styles' => '2.2.2@dda2ee426acd6d801d5b7fd1001cde9b5f790e15',
  'torann/geoip' => '1.0.13@9577c9d865050af39bb4c23795c9eb09bebb6476',
  'twilio/sdk' => '5.39.0@71432b4b4962cbed2fa01a8cf836ae5cea35e6f3',
  'vlucas/phpdotenv' => 'v3.6.0@1bdf24f065975594f6a117f0f1f6cabf1333b156',
  'waavi/sanitizer' => '1.0.10@d978b8f90de7a6ea40714b17fac0b1f59498f64f',
  'webmozart/assert' => '1.5.0@88e6d84706d09a236046d686bbea96f07b3a34f4',
  'zendframework/zend-diactoros' => '2.2.1@de5847b068362a88684a55b0dbb40d85986cfa52',
  'zircote/swagger-php' => '2.0.14@f2a00f26796e5cd08fd812275ba2db3d1e807663',
  'hamcrest/hamcrest-php' => 'v1.2.2@b37020aa976fa52d3de9aa904aa2522dc518f79c',
  'mockery/mockery' => '0.9.11@be9bf28d8e57d67883cba9fcadfcff8caab667f8',
  'sempro/phpunit-pretty-print' => '1.2.0@9b889cf6c24565aa6706f4429e6355ce7167aa70',
  'guesthouser/guesthouser-api' => 'dev-SRV-554-login-signup-apis-for-v1-7@1f73f1c996b8b10a5fe580018e0e9d0f9f192380',
);

    private function __construct()
    {
    }

    /**
     * @throws \OutOfBoundsException If a version cannot be located.
     *
     * @psalm-param key-of<self::VERSIONS> $packageName
     */
    public static function getVersion(string $packageName) : string
    {
        if (isset(self::VERSIONS[$packageName])) {
            return self::VERSIONS[$packageName];
        }

        throw new \OutOfBoundsException(
            'Required package "' . $packageName . '" is not installed: check your ./vendor/composer/installed.json and/or ./composer.lock files'
        );
    }
}