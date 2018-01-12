<?php

namespace IntegerNet\Solr\Indexer;

class Slice
{
    /**
     * @var int
     */
    private $sliceId;
    /**
     * @var int
     */
    private $totalNumberSlices;

    /**
     * @param int $sliceId
     * @param int $totalNumberSlices
     * @throws \InvalidArgumentException
     */
    public function __construct($sliceId, $totalNumberSlices)
    {
        if ((int) $sliceId < 1) {
            throw new \InvalidArgumentException('Invalid slice number. Slice numbers start at 1.');
        }
        if ((int)$sliceId > (int)$totalNumberSlices) {
            throw new \InvalidArgumentException(
                'Invalid slice number. Slice number must be less than or equal total number of slices.'
            );
        }
        $this->sliceId = (int) $sliceId;
        $this->totalNumberSlices = (int) $totalNumberSlices;
    }

    /**
     * @param string $expression
     * @return Slice
     * @throws \InvalidArgumentException
     */
    public static function fromExpression($expression)
    {
        $parts = explode('/', $expression);
        if (count($parts) !== 2 || !ctype_digit(implode('', $parts))) {
            throw new \InvalidArgumentException(
                "Invalid slice expression. Expression must contain two numbers separated by '/', e.g. '1/5'"
            );
        }
        return new self(...$parts);
    }

    /**
     * @return int
     */
    public function id()
    {
        return $this->sliceId;
    }

    /**
     * @return int
     */
    public function totalNumber()
    {
        return $this->totalNumberSlices;
    }
}