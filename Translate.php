<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Google\Cloud\Translate\V2\TranslateClient;

class Translate extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'translate:lang {lang}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Translates system words to provided language';

	private $path = 'lang';
	private $targetLanguage;

	/**
	 * Put your Google cloud key here
	 *
	 * @var string
	 */
	private $googleCloudKey = '';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle()
	{

		$defaultLocale = config('app.locale');
		$defaultLanguageFile = $this->path . '/' . $defaultLocale . '.json';
		$this->targetLanguage = $this->argument('lang');

		$targetLanguageFile = $this->path . '/' . $this->targetLanguage . '.json';
		$targetLanguageContent = [];

		// reads target language file if exists
		if (Storage::disk('resources')->exists($targetLanguageFile)) {

			$targetLanguageContent = json_decode(Storage::disk('resources')->get($targetLanguageFile), true);
		}

		// checks for default language file
		if (!Storage::disk('resources')->exists($defaultLanguageFile)) {

			$this->error('Default language file not found: ' . $defaultLocale . '.json');
			return 0;
		}

		$this->info('Starting the translation process...');

		// translates default language file
		$content = json_decode(Storage::disk('resources')->get($defaultLanguageFile), true);
		$target = [];

		if (empty($content)) {

			$this->error('Language file is empty');
			return 0;
		}

		$bar = $this->output->createProgressBar(count($content));
		$bar->start();

		foreach ($content as $key => $value) {

			// only translates new terms
			$target[$key] = empty($targetLanguageContent[$key]) ? $this->translate($value) : $targetLanguageContent[$key];

			$bar->advance();
		}

		$bar->finish();
		$this->newLine();

		Storage::disk('resources')->put($targetLanguageFile, json_encode($target, JSON_PRETTY_PRINT));

		$this->line('Success! New language file created: ' . $targetLanguageFile);

		return 0;
	}

	private function translate($term)
	{

		$translate = new TranslateClient([
			'key' => $this->googleCloudKey
		]);

		// Translate text from english.
		$result = $translate->translate($term, [
			'target' => $this->targetLanguage
		]);

		return $result['text'];
	}
}
