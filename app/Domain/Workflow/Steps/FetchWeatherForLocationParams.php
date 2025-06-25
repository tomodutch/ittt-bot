<?php

namespace App\Domain\Workflow\Steps;

final class FetchWeatherForLocationParams extends StepParams
{
    public function __construct(private string $location)
    {
    }
    
    public function getLocation()
    {
        return $this->location;
    }
    public static function from(array $data): self
    {
        $validated = self::validate($data, [
            'location' => ['required', 'string'],
        ]);

        return new self($validated['location']);
    }
}