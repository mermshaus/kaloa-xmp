<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

function print_header(string $header): void
{
    printf("%s\n", $header);
}

function print_value(?string $value): void
{
    if ($value === null) {
        $value = '(null)';
    }

    if ($value === '') {
        $value = '(empty string)';
    }

    echo '  ' . $value . "\n";

    echo "\n";
}

function print_values(array $values): void
{
    if (count($values) === 0) {
        echo '  (empty array)' . "\n";
    }

    foreach ($values as $index => $description) {
        echo '  ' . $index . ' : ' . $description . "\n";
    }

    echo "\n";
}

$reader = new Kaloa\Xmp\Reader();

$file = __DIR__ . '/metadata-test-file.jpg';

if (!file_exists($file)) {
    $url =
        'https://upload.wikimedia.org/wikipedia/commons/5/5a/Metadata_test_file_-_includes_data_in_IIM%2C_XMP%2C_and_Exif.jpg';

    echo 'This script expects a demo image to be present at: ' . $file . "\n";
    echo "\n";
    echo 'Run the following command to place one there:' . "\n";
    echo "\n";

    echo '  curl -o ' . escapeshellarg($file) . ' ' . escapeshellarg($url) . "\n";
    exit(1);
}

$doc = $reader->getXmpDocument(fopen($file, 'rb'));

$dcProps = $doc->getDublinCoreProperties();
$exifProps = $doc->getExifProperties();

echo '======= DC Properties =======' . "\n\n";

print_header('Contributor');
print_values($dcProps->getContributor());

print_header('Coverage');
print_value($dcProps->getCoverage());

print_header('Creator');
print_values($dcProps->getCreator());

print_header('Date');
print_values($dcProps->getDate());

print_header('Description');
print_values($dcProps->getDescription());

print_header('Format');
print_value($dcProps->getFormat());

print_header('Identifier');
print_value($dcProps->getIdentifier());

print_header('Language');
print_values($dcProps->getLanguage());

print_header('Publisher');
print_values($dcProps->getPublisher());

print_header('Relation');
print_values($dcProps->getRelation());

print_header('Rights');
print_values($dcProps->getRights());

print_header('Title');
print_values($dcProps->getTitle());

print_header('Type');
print_values($dcProps->getType());

print_header('Source');
print_value($dcProps->getSource());

print_header('Subject');
print_values($dcProps->getSubject());

echo '======= EXIF Properties =======' . "\n\n";

print_header('DateTimeOriginal');
print_value($exifProps->getDateTimeOriginal()?->format(DATE_ATOM));

print_header('ExifVersion');
print_value($exifProps->getExifVersion());

print_header('PixelXDimension');
print_value($exifProps->getPixelXDimension());

print_header('PixelYDimension');
print_value($exifProps->getPixelYDimension());
