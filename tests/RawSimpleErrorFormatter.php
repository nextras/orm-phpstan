<?php declare(strict_types = 1);

namespace NextrasTests\OrmPhpStan;

use PHPStan\Command\AnalysisResult;
use PHPStan\Command\ErrorFormatter\ErrorFormatter;
use PHPStan\Command\Output;

class RawSimpleErrorFormatter implements ErrorFormatter
{
	public function formatErrors(AnalysisResult $analysisResult, Output $style): int
	{
		if (!$analysisResult->hasErrors()) {
			return 0;
		}

		foreach ($analysisResult->getNotFileSpecificErrors() as $notFileSpecificError) {
			$style->writeLineFormatted(sprintf('?:?:%s', $notFileSpecificError));
		}

		foreach ($analysisResult->getFileSpecificErrors() as $fileSpecificError) {
			$style->writeLineFormatted(
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
