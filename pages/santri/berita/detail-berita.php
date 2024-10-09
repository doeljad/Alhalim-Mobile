<?php
if (isset($_GET['link'])) {
    $articleLink = urldecode($_GET['link']);

    // Ensure the link starts with 'http'
    if (strpos($articleLink, 'http') !== 0) {
        $articleLink = 'https://nu.or.id' . $articleLink;
    }

    // Retrieve the article HTML
    $articleHtml = file_get_contents($articleLink);

    if ($articleHtml === false) {
        die('Error retrieving the article.');
    }

    // Convert encoding to UTF-8
    $articleHtml = mb_convert_encoding($articleHtml, 'UTF-8', 'auto');

    // Save HTML for debugging purposes
    file_put_contents('pages/santri/berita/cache/debug.html', $articleHtml);

    $doc = new DOMDocument();
    @$doc->loadHTML($articleHtml);

    $xpath = new DOMXPath($doc);

    // Extract the title
    $titleNode = $xpath->query("//h1")->item(0);
    $title = $titleNode ? $titleNode->nodeValue : "Title not found";

    // Extract content from the element with id="detail-content"
    $contentNode = $xpath->query("//div[@id='detail-content']")->item(0);
    if ($contentNode) {
        $content = $doc->saveHTML($contentNode);

        // Load content into a new DOMDocument for further processing
        $contentDoc = new DOMDocument();
        @$contentDoc->loadHTML('<html><body>' . $content . '</body></html>');

        $contentXpath = new DOMXPath($contentDoc);

        // Remove specific elements by class and ID
        $unwantedClasses = ['print:hidden', 'adsense']; // Add more classes as needed
        foreach ($unwantedClasses as $class) {
            $nodes = $contentXpath->query("//div[contains(@class, '$class')]");
            foreach ($nodes as $node) {
                $node->parentNode->removeChild($node);
            }
        }

        // Apply Arabic text styling
        $content = str_replace('<p dir="rtl">', '<p class="arabic-text">', $content);

        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Save cleaned HTML to inspect
        file_put_contents('pages/santri/berita/cache/cleaned_content.html', $content);
    } else {
        $content = "Content not found";
    }

    // Extract the specific image block outside of #detail-content
    $imageBlockNode = $xpath->query("//div[contains(@class, 'relative') and contains(@class, 'aspect-video')]")->item(0);

    // Debug: Save image block HTML to inspect
    if ($imageBlockNode) {
        $imageBlockHtml = $doc->saveHTML($imageBlockNode);
        file_put_contents('pages/santri/berita/cache/image_block_debug.html', $imageBlockHtml); // Save for debugging

        // Extract the image source
        $imageDoc = new DOMDocument();
        @$imageDoc->loadHTML('<html><body>' . $imageBlockHtml . '</body></html>');
        $imageXpath = new DOMXPath($imageDoc);
        $imgNode = $imageXpath->query("//img")->item(0);

        if ($imgNode) {
            $imageSrc = $imgNode->getAttribute('src');
        } else {
            $imageSrc = 'Image not found';
        }
    } else {
        $imageSrc = 'Image block not found';
    }

    // Extract date and time
    $dateNode = $xpath->query("//p[contains(@class, 'mt-3') and contains(@class, 'text-base')]")->item(0);
    if ($dateNode) {
        $dateText = $dateNode->nodeValue;
        // Parse and format date
        preg_match('/(\w+), (\d+ \w+ \d+) \| (\d+:\d+ WIB)/', $dateText, $matches);
        if (count($matches) === 4) {
            $day = $matches[1];
            $date = $matches[2];
            $time = $matches[3];
            $formattedDate = "$day, $date at $time";
        } else {
            $formattedDate = "Date not found";
        }
    } else {
        $formattedDate = "Date not found";
    }
} else {
    echo "No article link provided!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .arabic-text {
            font-family: 'Amiri', serif;
            font-size: 1.5rem;
            background-color: #f0f4f8;
            padding: 10px;
            border-radius: 12px;
            direction: rtl;
            text-align: right;
        }

        figure.image {
            margin: 0;
            padding: 0;
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        figure.image img {
            display: block;
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        figure.image figcaption {
            padding: 10px;
            background-color: rgba(0, 0, 0, 0.1);
            color: #333;
            font-size: 0.9rem;
            text-align: center;
            border-top: 1px solid #ddd;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }
    </style>
</head>

<body>
    <div class="container my-2">
        <h3 class="mb-4"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h3>
        <p class="mt-3 text-base text-gray-400 dark:text-gray-300 sm:text-xs"><?php echo htmlspecialchars($formattedDate, ENT_QUOTES, 'UTF-8'); ?></p>
        <div class="my-4">
            <?php if ($imageSrc && $imageSrc !== 'Image not found' && $imageSrc !== 'Image block not found'): ?>
                <img src="<?php echo htmlspecialchars($imageSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="Extracted Image" class="img-fluid rounded-4">
            <?php else: ?>
                <div class="alert alert-warning alert-message" role="alert">
                    Image not found or image block not available.
                </div>
            <?php endif; ?>
        </div>
        <div class="content">
            <?php echo $content; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var idsToRemove = ['paragraph-news-0', 'paragraph-news-1', 'paragraph-news-2'];
            idsToRemove.forEach(function(id) {
                var element = document.getElementById(id);
                if (element) {
                    element.parentNode.removeChild(element);
                }
            });

            var classesToRemove = ['print:hidden', 'adsense'];
            classesToRemove.forEach(function(className) {
                var elements = document.getElementsByClassName(className);
                while (elements.length > 0) {
                    elements[0].parentNode.removeChild(elements[0]);
                }
            });
        });
    </script>
</body>

</html>