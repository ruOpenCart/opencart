<?php
/**
 * Собирает ocn_slider.ocmod.zip из каталога ocn_slider.ocmod.
 * Рабочее дерево не меняется.
 *
 * php ocn/slider/build.php
 */

$scriptDir = realpath(__DIR__);

if ($scriptDir === false) {
	fwrite(STDERR, "Не удалось определить каталог скрипта.\n");
	exit(1);
}

$packageRoot = $scriptDir . DIRECTORY_SEPARATOR . 'ocn_slider.ocmod';
$installPath = $packageRoot . DIRECTORY_SEPARATOR . 'install.json';
$installRaw = file_get_contents($installPath);

if ($installRaw === false) {
	fwrite(STDERR, "Не удалось прочитать install.json\n");
	exit(1);
}

$install = json_decode($installRaw, true);

if (!is_array($install) || ($install['code'] ?? '') !== 'ocn_slider' || !is_string($install['version'] ?? null) || $install['version'] === '') {
	fwrite(STDERR, "В install.json нет кода ocn_slider и версии.\n");
	exit(1);
}

$buildRoot = $scriptDir . DIRECTORY_SEPARATOR . 'build';
$stagingDir = $buildRoot . DIRECTORY_SEPARATOR . 'staging';
$distDir = $scriptDir . DIRECTORY_SEPARATOR . 'dist';
$zipPath = $distDir . DIRECTORY_SEPARATOR . 'ocn_slider.ocmod.zip';

removeTree($buildRoot);

if (!mkdir($stagingDir, 0777, true) && !is_dir($stagingDir)) {
	fwrite(STDERR, "Не удалось создать каталог сборки.\n");
	exit(1);
}

$copied = copyPackage($packageRoot, $stagingDir);

if (!is_dir($distDir) && !mkdir($distDir, 0777, true) && !is_dir($distDir)) {
	fwrite(STDERR, "Не удалось создать dist.\n");
	exit(1);
}

if (is_file($zipPath)) {
	unlink($zipPath);
}

$entries = writeZip($stagingDir, $zipPath);

foreach (['install.json', 'admin/controller/module/slider.php', 'catalog/controller/module/slider.php', 'catalog/view/javascript/slider.js', 'catalog/view/stylesheet/slider.css', 'catalog/view/template/module/slider.twig'] as $required) {
	if (!in_array($required, $entries, true)) {
		fwrite(STDERR, "В zip нет {$required}\n");
		exit(1);
	}
}

fwrite(STDOUT, 'Версия: ' . $install['version'] . PHP_EOL);
fwrite(STDOUT, 'Файлов пакета: ' . $copied . PHP_EOL);
fwrite(STDOUT, 'Архив: ' . $zipPath . PHP_EOL);

function removeTree(string $dir): void {
	if (!file_exists($dir)) {
		return;
	}

	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
		RecursiveIteratorIterator::CHILD_FIRST
	);

	foreach ($iterator as $item) {
		if ($item->isDir()) {
			rmdir($item->getPathname());
		} else {
			unlink($item->getPathname());
		}
	}

	rmdir($dir);
}

function copyPackage(string $packageRoot, string $stagingDir): int {
	$count = 0;
	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($packageRoot, FilesystemIterator::SKIP_DOTS)
	);

	foreach ($iterator as $item) {
		if (!$item->isFile()) {
			continue;
		}

		$relative = substr(str_replace('\\', '/', $item->getPathname()), strlen(str_replace('\\', '/', rtrim($packageRoot, '/\\'))) + 1);

		foreach (explode('/', $relative) as $part) {
			if ($part === '.git' || $part === 'node_modules' || $part === 'vendor') {
				continue 2;
			}
		}

		$destination = $stagingDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
		$directory = dirname($destination);

		if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
			fwrite(STDERR, "Не удалось создать каталог.\n");
			exit(1);
		}

		if (!copy($item->getPathname(), $destination)) {
			fwrite(STDERR, "Не удалось скопировать файл.\n");
			exit(1);
		}

		$count++;
	}

	if ($count === 0) {
		fwrite(STDERR, "Пакет пуст.\n");
		exit(1);
	}

	return $count;
}

function writeZip(string $stagingDir, string $zipPath): array {
	$names = [];

	if (class_exists(ZipArchive::class)) {
		$zip = new ZipArchive();

		if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
			fwrite(STDERR, "Не удалось создать zip.\n");
			exit(1);
		}

		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($stagingDir, FilesystemIterator::SKIP_DOTS)
		);

		foreach ($iterator as $item) {
			if (!$item->isFile()) {
				continue;
			}

			$relative = substr(str_replace('\\', '/', $item->getPathname()), strlen(str_replace('\\', '/', rtrim($stagingDir, '/\\'))) + 1);

			if (!$zip->addFile($item->getPathname(), $relative)) {
				fwrite(STDERR, "Не удалось добавить файл в zip.\n");
				exit(1);
			}

			$names[] = $relative;
		}

		$zip->close();

		return $names;
	}

	$builder = new ZipBuilder();
	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($stagingDir, FilesystemIterator::SKIP_DOTS)
	);

	foreach ($iterator as $item) {
		if (!$item->isFile()) {
			continue;
		}

		$relative = substr(str_replace('\\', '/', $item->getPathname()), strlen(str_replace('\\', '/', rtrim($stagingDir, '/\\'))) + 1);
		$contents = file_get_contents($item->getPathname());

		if ($contents === false) {
			fwrite(STDERR, "Не удалось прочитать файл пакета.\n");
			exit(1);
		}

		$builder->addFile($relative, $contents);
		$names[] = $relative;
	}

	$builder->save($zipPath);

	return $names;
}

final class ZipBuilder {
	/** @var list<array{name:string,method:int,crc:int,size:int,compSize:int,data:string}> */
	private array $files = [];

	public function addFile(string $name, string $contents): void {
		$name = str_replace('\\', '/', $name);
		$size = strlen($contents);
		$deflated = gzdeflate($contents, 9);

		if ($deflated === false || strlen($deflated) >= $size) {
			$method = 0;
			$stored = $contents;
		} else {
			$method = 8;
			$stored = $deflated;
		}

		$this->files[] = [
			'name'     => $name,
			'method'   => $method,
			'crc'      => crc32($contents),
			'size'     => $size,
			'compSize' => strlen($stored),
			'data'     => $stored,
		];
	}

	public function save(string $path): void {
		$now = getdate();
		$dosTime = ($now['hours'] << 11) | ($now['minutes'] << 5) | intdiv($now['seconds'], 2);
		$dosDate = (($now['year'] - 1980) << 9) | ($now['mon'] << 5) | $now['mday'];
		$local = '';
		$central = '';
		$offset = 0;

		foreach ($this->files as $file) {
			$nameLen = strlen($file['name']);
			$header = pack('VvvvvvVVVvv', 0x04034b50, 20, 0, $file['method'], $dosTime, $dosDate, $file['crc'], $file['compSize'], $file['size'], $nameLen, 0);
			$local .= $header . $file['name'] . $file['data'];
			$central .= pack('VvvvvvvVVVvvvvvVV', 0x02014b50, 20, 20, 0, $file['method'], $dosTime, $dosDate, $file['crc'], $file['compSize'], $file['size'], $nameLen, 0, 0, 0, 0, 0100644 << 16, $offset);
			$central .= $file['name'];
			$offset += strlen($header) + $nameLen + $file['compSize'];
		}

		$eocd = pack('VvvvvVVv', 0x06054b50, 0, 0, count($this->files), count($this->files), strlen($central), $offset, 0);

		if (file_put_contents($path, $local . $central . $eocd) === false) {
			fwrite(STDERR, "Не удалось записать zip.\n");
			exit(1);
		}
	}
}
