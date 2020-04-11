<?php
header("Content-Type: text/xml");
include "estimator.php";


//echo hhb_xml_encode(covid19ImpactEstimator($decoded));




echo xml_encode(covid19ImpactEstimator($decoded));
function xml_encode($mixed, $domElement=null, $DOMDocument=null) {
    if (is_null($DOMDocument)) {
        $DOMDocument =new DOMDocument;
        $DOMDocument->formatOutput = true;
        xml_encode($mixed, $DOMDocument, $DOMDocument);
        return $DOMDocument->saveXML();
    }
    else {
        if (is_array($mixed)) {
            foreach ($mixed as $index => $mixedElement) {
                if (is_int($index)) {
                    if ($index === 0) {
                        $node = $domElement;
                    }
                    else {
                        $node = $DOMDocument->createElement($domElement->tagName);
                        $domElement->parentNode->appendChild($node);
                    }
                }
                else {
                    $plural = $DOMDocument->createElement($index);
                    $domElement->appendChild($plural);
                    $node = $plural;
                }

                xml_encode($mixedElement, $node, $DOMDocument);
            }
        }
        else {
            $domElement->appendChild($DOMDocument->createTextNode($mixed));
        }
    }
}


$time2 = microtime(true);

$exe_time = ($time2- $_SERVER["REQUEST_TIME_FLOAT"])* 1000;
$logMessage = $_SERVER['REQUEST_METHOD']. "\t\t".$_SERVER['REQUEST_URI']. "\t\t".http_response_code()."\t\t". round($exe_time,2)." ms";
file_put_contents('logs.txt', $logMessage."\n", FILE_APPEND | LOCK_EX);


function hhb_xml_encode(array $arr, string $name_for_numeric_keys = 'val'): string {
    if (empty ( $arr )) {
        return '';
    }
    $is_iterable_compat = function ($v): bool {
        return is_array ( $v ) || ($v instanceof \Traversable);
    };
    $isAssoc = function (array $arr): bool {
        if (array () === $arr)
            return false;
        return array_keys ( $arr ) !== range ( 0, count ( $arr ) - 1 );
    };
    $endsWith = function (string $haystack, string $needle): bool {
        $length = strlen ( $needle );
        if ($length == 0) {
            return true;
        }
        return (substr ( $haystack, - $length ) === $needle);
    };
    $formatXML = function (string $xml) use ($endsWith): string {
        $domd = new DOMDocument ( '1.0', 'UTF-8' );
        $domd->preserveWhiteSpace = false;
        $domd->formatOutput = true;
        $domd->loadXML ( '<root>' . $xml . '</root>' );
        $ret = trim ( $domd->saveXML ( $domd->getElementsByTagName ( "root" )->item ( 0 ) ) );
        $full = trim ( substr ( $ret, strlen ( '<root>' ), - strlen ( '</root>' ) ) );
        $ret = '';
        foreach ( explode ( "\n", $full ) as $line ) {
            if (substr ( $line, 0, 2 ) === '  ') {
                $ret .= substr ( $line, 2 ) . "\n";
            } else {
                $ret .= $line . "\n";
            }
        }
        $ret = trim ( $ret );
        return $ret;
    };

    $iterator = $arr;
    $domd = new DOMDocument ();
    $root = $domd->createElement ( 'root' );
    foreach ( $iterator as $key => $val ) {

        $ele = $domd->createElement ( is_int ( $key ) ? $name_for_numeric_keys : $key );
        if (! empty ( $val ) || $val === '0') {
            if ($is_iterable_compat ( $val )) {
                $asoc = $isAssoc ( $val );
                $tmp = hhb_xml_encode ( $val, is_int ( $key ) ? $name_for_numeric_keys : $key );

                $tmp = @DOMDocument::loadXML ( '<root>' . $tmp . '</root>' );
                foreach ( $tmp->getElementsByTagName ( "root" )->item ( 0 )->childNodes ?? [ ] as $tmp2 ) {
                    $tmp3 = $domd->importNode ( $tmp2, true );
                    if ($asoc) {
                        $ele->appendChild ( $tmp3 );
                    } else {
                        $root->appendChild ( $tmp3 );
                    }
                }
                unset ( $tmp, $tmp2, $tmp3 );
                if (! $asoc) {

                    continue;
                }
            } else {
                $ele->textContent = $val;
            }
        }
        $root->appendChild ( $ele );
    }
    $domd->preserveWhiteSpace = false;
    $domd->formatOutput = true;
    $ret = trim ( $domd->saveXML ( $root ) );
    $ret = trim ( substr ( $ret, strlen ( '<root>' ), - strlen ( '</root>' ) ) );

    $ret = $formatXML ( $ret );
    /*return '<?xml version="1.0" ?><root>' . $ret . '</root>';*/
    return $ret;
}