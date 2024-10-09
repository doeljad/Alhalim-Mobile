<?php include('pages/inc/sntr-sidebar.php') ?>
<div class="phone">
    <input type="radio" name="s" id="s1" value="dashboard">
    <input type="radio" name="s" id="s2" value="berita" checked="checked">
    <input type="radio" name="s" id="s3" value="belanja">
    <input type="radio" name="s" id="s4" value="notifikasi">
    <input type="radio" name="s" id="s5" value="pengaturan">

    <label for="s1" data-page="dashboard">
        <i class="fas fa-home"></i>
        <span>Dashboard</span>
    </label>
    <label for="s2" data-page="berita">
        <i class="fas fa-newspaper"></i>
        <span>Berita</span>
    </label>
    <label for="s3" data-page="belanja">
        <i class="fas fa-shopping-cart"></i>
        <span>Belanja</span>
    </label>
    <label for="s4" data-page="notifikasi" class="position-relative">
        <i class="fas fa-bell"></i>
        <span>Notifikasi</span>
        <?php if ($unread_count > 0): ?>
            <span class="position-absolute top-20 start-90 badge rounded-pill bg-danger"
                style="margin-left: 25px; margin-top: -10px; font-size: 0.7rem; padding: 2px 6px;">
                <?= $unread_count ?>
                <span class="visually-hidden">unread messages</span>
            </span>
        <?php endif; ?>
    </label>
    <label for="s5" data-page="pengaturan">
        <i class="fas fa-gear"></i>
        <span>Pengaturan</span>
    </label>

    <div class="circle bg-gradient-primary"></div>
    <div class="phone_content">
        <div class="phone_bottom">
            <span class="indicator"></span>
        </div>
    </div>
</div>
<div class="container mt-3">
    <h3 class="mb-2 text-center" id="headingTitle">Berita Terkini</h3>
    <div class="text-center mb-4">
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#filterModal">
            <i class="fa fa-filter"></i> Filter Sumber Berita
        </button>
    </div>

    <div class="row">
        <?php
        $source = isset($_GET['source']) ? $_GET['source'] : 'cnn/terbaru';
        $category = isset($_GET['category']) ? $_GET['category'] : '';

        $headingTitle = "Berita Terkini";
        if (!empty($category)) {
            $sourceText = explode('/', $source)[0];
            $headingTitle = "Berita " . htmlspecialchars($category, ENT_QUOTES, 'UTF-8') . " dari " . ucfirst($sourceText);
        } elseif (!empty($source)) {
            $sourceText = explode('/', $source)[0];
            $headingTitle = "Berita Terkini dari " . ucfirst($sourceText);
        }
        ?>
        <script>
            document.getElementById('headingTitle').textContent = "<?php echo $headingTitle; ?>";
        </script>

        <?php
        if ($source === 'NU Online') {

            $url = "https://nu.or.id/$category";
            $html = file_get_contents($url);
            $doc = new DOMDocument();
            @$doc->loadHTML($html);

            $xpath = new DOMXPath($doc);
            $articles = $xpath->query("//div[contains(@class, 'border-gray2')]");

            if ($articles->length > 0):
                foreach ($articles as $article):
                    $titleNode = $xpath->query(".//h2[@class='medium text-black-900 mt-2 text-base font-bold dark:text-gray-100 sm:text-sm']", $article)->item(0);
                    $linkNode = $xpath->query(".//a[@href]", $article)->item(1);
                    $descriptionNode = $xpath->query(".//p[@class='medium font-inter mt-2 text-xs text-gray-400 dark:text-gray-300']", $article)->item(0);
                    $imgNode = $xpath->query(".//img", $article)->item(0);

                    if ($titleNode && $linkNode && $descriptionNode && $imgNode) {
                        $title = $titleNode->nodeValue;
                        $link = $linkNode->getAttribute('href');
                        $description = $descriptionNode->nodeValue;
                        $imgSrc = $imgNode->getAttribute('src');
                    } else {
                        continue;
                    }
        ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-light shadow-sm">
                            <img src="<?php echo htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8'); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>" style="height: 200px; object-fit: cover;"> <!-- Kurangi tinggi gambar -->
                            <div class="card-body p-2"> <!-- Kurangi padding pada card body -->
                                <h5 class="card-title" style="font-size: 1rem;"> <!-- Kurangi ukuran font title -->
                                    <a href="?page=detail-berita&link=<?php echo htmlspecialchars($link, ENT_QUOTES, 'UTF-8'); ?>" style="text-decoration: none;">
                                        <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </h5>
                                <p class="card-text" style="font-size: 0.875rem;"><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></p> <!-- Kurangi ukuran font deskripsi -->
                                <a href="?page=detail-berita&link=<?php echo urlencode($link); ?>" class="btn btn-primary btn-sm">Baca Selengkapnya</a> <!-- Gunakan tombol lebih kecil -->
                            </div>
                        </div>
                    </div>
                <?php
                endforeach;
            else:
                ?>
                <p>Tidak ada berita yang ditemukan.</p>
            <?php endif; ?>
        <?php
        } else {
            if (!empty($category)) {
                // Remove 'terbaru' from the source if it's present
                $sourceParts = explode('/', $source);
                $sourceParts = array_slice($sourceParts, 0, -1); // Remove the last part ('terbaru')
                $source = implode('/', $sourceParts) . '/' . $category;
            }

            $apiUrl = "https://api-berita-indonesia.vercel.app/$source";

            $response = file_get_contents($apiUrl);
            if ($response) {
                $newsData = json_decode($response, true);

                // Check if API returns "Not found"
                if (isset($newsData['success']) && $newsData['success'] === false && $newsData['message'] === "Not found") {
                    echo "<div class='col-12'><p class='alert alert-danger'>Sumber berita tidak ditemukan.</p></div>";
                } elseif (isset($newsData['data'])) {
                    $data = $newsData['data'];

                    // Check if 'data' is an array and has 'posts'
                    if (is_array($data) && isset($data['posts']) && is_array($data['posts'])) {
                        foreach ($data['posts'] as $newsItem) {
                            if (isset($newsItem['title'], $newsItem['description'], $newsItem['link'])) {
                                echo "<div class='col-md-4 mb-4'>";
                                echo "<div class='card h-100 border-light shadow-sm'>";

                                if (!empty($newsItem['thumbnail'])) {
                                    echo "<img src='" . htmlspecialchars($newsItem['thumbnail'], ENT_QUOTES, 'UTF-8') . "' class='card-img-top' alt='Thumbnail' style='height: 200px; object-fit: cover;'>";
                                }

                                echo "<div class='card-body p-2'>"; // Menambahkan padding kecil pada card body
                                echo "<h5 class='card-title' style='font-size: 1rem;'>"; // Kurangi ukuran font title
                                echo "<a href='?page=detail-berita&link=" . htmlspecialchars($newsItem['link'], ENT_QUOTES, 'UTF-8') . "' style='text-decoration: none;'>";
                                echo htmlspecialchars($newsItem['title'], ENT_QUOTES, 'UTF-8');
                                echo "</a>";
                                echo "</h5>";

                                echo "<p class='card-text' style='font-size: 0.875rem;'>" . htmlspecialchars($newsItem['description'], ENT_QUOTES, 'UTF-8') . "</p>"; // Kurangi ukuran font deskripsi

                                echo "<a href='?page=detail-berita&link=" . urlencode($newsItem['link']) . "' class='btn btn-primary btn-sm' target='_blank'>Baca Selengkapnya</a>"; // Gunakan tombol lebih kecil
                                echo "</div>";

                                echo "</div>";
                                echo "</div>";
                            } else {
                                echo "<div class='col-12'><p class='alert alert-warning'>Item berita tidak memiliki informasi yang lengkap.</p></div>";
                            }
                        }
                    } else {
                        echo "<div class='col-12'><p class='alert alert-info'>Tidak ada berita yang ditemukan.</p></div>";
                    }
                } else {
                    echo "<div class='col-12'><p class='alert alert-warning'>Format data yang diterima dari API tidak valid atau data kosong.</p></div>";
                }
            } else {
                echo "<div class='col-12'><p class='alert alert-danger'>Gagal mengambil data dari API.</p></div>";
            }
        }
        ?>
    </div>
</div>
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Sumber Berita</h5>
            </div>
            <div class="modal-body">
                <form id="filterForm" method="get" action="santri.php?page=berita">
                    <div class="mb-3">
                        <label for="sourceSelect" class="form-label">Pilih Sumber Berita</label>
                        <select class="form-select" id="sourceSelect" name="source">
                            <option value="">Pilih Berita</option>
                            <option value="NU Online">NU Online</option>
                            <option value="cnn/terbaru">CNN</option>
                            <option value="antara/terbaru">Antara</option>
                            <option value="jpnn/terbaru">JPNN</option>
                            <option value="kumparan/terbaru">Kumparan</option>
                            <option value="republika/terbaru">Republika</option>
                            <option value="sindonews/terbaru">Sindonews</option>
                            <option value="tempo/nasional">Tempo</option>
                            <option value="tribun/terbaru">Tribun</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="categorySelect" class="form-label">Pilih Kategori</label>
                        <select class="form-select" id="categorySelect" name="category">
                            <option value="">Semua</option>
                            <!-- Categories for each source will be populated dynamically -->
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Terapkan Filter</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    document.getElementById('filterForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const source = document.getElementById('sourceSelect').value;
        const category = document.getElementById('categorySelect').value;
        const query = new URLSearchParams({
            source,
            category
        }).toString();
        window.location.href = `?page=berita&${query}`;
    });

    document.getElementById('sourceSelect').addEventListener('change', function() {
        const source = this.value;
        const categorySelect = document.getElementById('categorySelect');
        let options = '';

        switch (source) {

            case 'NU Online':
                options = `
                        <option value="">Semua</option>
                        <option value="terbaru">Terbaru</option>
                        <option value="Internasional">Internasional</option>
                        <option value="Nasional">Nasional</option>
                        <option value="Daerah">Daerah</option>
                        <option value="Fragmen">Fragmen</option>
                        <option value="Khutbah">Khutbah</option>
                        <option value="Syariah">Syariah</option>
                        <option value="Tafsir">Tafsir</option>
                        <option value="Hikmah">Hikmah</option>
                        <option value="Opini">Opini</option>
                        <option value="Tokoh">Tokoh</option>
                        <option value="Hikmah">Hikmah</option>
                        <option value="Kesehatan">Kesehatan</option>
                        <option value="Cerpen">Cerpen</option>
                        <option value="Ramadhan">Ramadhan</option>
                        <option value="Pustaka">Pustaka</option>
                        <option value="Humor">Humor</option>
                    `;
                break;
            case 'antara/terbaru':
                options = `
                        <option value="">Semua</option>
                        <option value="terbaru">Terbaru</option>
                        <option value="politik">Politik</option>
                        <option value="hukum">Hukum</option>
                        <option value="ekonomi">Ekonomi</option>
                        <option value="bola">Bola</option>
                        <option value="olahraga">Olahraga</option>
                        <option value="humaniora">Humaniora</option>
                        <option value="lifestyle">Lifestyle</option>
                        <option value="hiburan">Hiburan</option>
                        <option value="tekno">Tekno</option>
                        <option value="otomotif">Otomotif</option>
                    `;
                break;
            case 'cnn/terbaru':
                options = `
                        <option value="">Semua</option>
                        <option value="terbaru">Terbaru</option>
                        <option value="nasional">Nasional</option>
                        <option value="internasional">Internasional</option>
                        <option value="ekonomi">Ekonomi</option>
                        <option value="olahraga">Olahraga</option>
                        <option value="hiburan">Hiburan</option>
                        <option value="teknologi">Teknologi</option>
                    `;
                break;
            case 'jpnn/terbaru':
                options = `
                        <option value="terbaru">Terbaru</option>
                    `;
                break;
            case 'kumparan/terbaru':
                options = `
                        <option value="terbaru">Terbaru</option>
                    `;
                break;
            case 'republika/terbaru':
                options = `
                        <option value="">Semua</option>
                        <option value="terbaru">Terbaru</option>
                        <option value="internasional">Internasional</option>
                        <option value="islam">Islam</option>
                    `;
                break;
            case 'sindonews/terbaru':
                options = `
                        <option value="">Semua</option>
                        <option value="terbaru">Terbaru</option>
                        <option value="nasional">Nasional</option>
                        <option value="metro">Metro</option>
                        <option value="daerah">Daerah</option>
                        <option value="otomotif">Otomotif</option>
                        <option value="sains">Sains</option>
                        <option value="tekno">Tekno</option>
                    `;
                break;

            case 'tempo/nasional':
                options = `
                        <option value="">Semua</option>
                        <option value="nasional">Nasional</option>
                        <option value="metro">Metro</option>
                        <option value="bisnis">Bisnis</option>
                        <option value="bola">Bola</option>
                    `;
                break;
            case 'tribun/terbaru':
                options = `
                        <option value="terbaru">Terbaru</option>
                    `;
                break;
            default:
                options = `<option value="">Semua</option>`;
        }

        categorySelect.innerHTML = options;
    });

    document.addEventListener("DOMContentLoaded", function() {
        updateHeading(); // Update the heading on page load
    });
</script>