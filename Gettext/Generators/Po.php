<?php
namespace Gettext\Generators;

use Gettext\Entries;

class Po extends Generator {
	static public function generate (Entries $entries) {
		$lines = array('msgid ""', 'msgstr ""');

		$headers = array_replace(array(
			'Project-Id-Version' => '',
			'Report-Msgid-Bugs-To' => '',
			'Last-Translator' => '',
			'Language-Team' => '',
			'Content-Type' => 'text/plain; charset=UTF-8',
			'Content-Transfer-Encoding' => '8bit'
		), $entries->getHeaders());

		$headers['POT-Creation-Date'] = $headers['PO-Revision-Date'] = date('c');

		foreach ($headers as $name => $value) {
			$lines[] = '"'.$name.': '.$value.'"';
		}

		$lines[] = '';

		//Entries
		foreach ($entries as $translation) {
			if ($translation->hasComments()) {
				foreach ($translation->getComments() as $comment) {
					$lines[] = '# '.$comment;
				}
			}

			if ($translation->hasReferences()) {
				foreach ($translation->getReferences() as $reference) {
					$lines[] = '#: '.$reference[0].':'.$reference[1];
				}
			}

			if ($translation->hasContext()) {
				$lines[] = 'msgctxt '.self::quote($translation->getContext());
			}

			if ($translation->hasPlural()) {
				$lines[] = 'msgid '.self::quote($translation->getOriginal());
				$lines[] = 'msgid_plural '.self::quote($translation->getPlural());
				$lines[] = 'msgstr[0] '.self::quote($translation->getTranslation());

				foreach ($translation->getPluralTranslation() as $k => $v) {
					$lines[] = 'msgstr['.($k + 1).'] '.self::quote($v);
				}
			} else {
				$lines[] = 'msgid '.self::quote($translation->getOriginal());
				$lines[] = 'msgstr '.self::quote($translation->getTranslation());
			}
			
			$lines[] = '';
		}

		return implode("\n", $lines);
	}

	static private function quote ($string) {
		return '"'.str_replace(array("\r", "\n", '"'), array('', '\n', '\\"'), $string).'"';
	}
}
