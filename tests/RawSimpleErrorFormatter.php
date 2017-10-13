<?php declare(strict_types = 1);

namespace NextrasTests\OrmPhpStan;

use PHPStan\Command\AnalysisResult;
use PHPStan\Command\ErrorFormatter\ErrorFormatter;
use Symfony\Component\Console\Style\OutputStyle;


class RawSimpleErrorFormatter implements ErrorFormatter
{
	public function formatErrors(AnalysisResult $analysisResult, OutputStyle $style): int
	{
		if (!$analysisResult->hasErrors()) {
			return 0;
		}

		foreach ($analysisResult->getNotFileSpecificErrors() as $notFileSpecificError) {
			$style->writeln(sprintf('?:?:%s', $notFileSpecificError));
		}

		foreach ($analysisResult->getFileSpecificErrors() as $fileSpecificError) {
			$style->writeln(
				sprintf(
					'%s:%d:%s',
					str_replace([__DIR__, '\\'], ['', '/'], $fileSpecificError->getFile()),
					$fileSpecificError->getLine() !== null ? $fileSpecificError->getLine() : '?',
					$fileSpecificError->getMessage()
				)
			);
		}

		return 1;
	}
}
