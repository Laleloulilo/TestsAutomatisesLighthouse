SET /p site=Site a analyser (http inclus):
echo site %site%
php -f ./code/listingPagesViaSitemapVersionBatch.php %site%
lighthouse-batch -f ./code/fichierliste.txt -o "./report" --html |more
php -f ./code/decodeResult.php %site%