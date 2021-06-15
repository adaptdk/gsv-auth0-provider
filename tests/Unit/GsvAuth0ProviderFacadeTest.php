<?php

namespace Adaptdk\GsvAuth0Provider\Tests\Unit;

use Adaptdk\GsvAuth0Provider\Facades\GsvAuth0Provider as FacadesGsvAuth0Provider;
use Adaptdk\GsvAuth0Provider\GsvAuth0Provider;
use Adaptdk\GsvAuth0Provider\Tests\TestCase;

class GsvAuth0ProviderFacadeTest extends TestCase
{
    /** @test */
    public function it_resolves_to_the_correct_class()
    {
        $user = FacadesGsvAuth0Provider::getFacadeRoot();

        $this->assertInstanceOf(GsvAuth0Provider::class, $user);
    }
}
