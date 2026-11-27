<?php

namespace Tests\Unit;

use App\Models\Flight;
use App\Models\PetBooking;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class PetModelTest extends TestCase
{
    public function test_pet_booking_transport_type_names(): void
    {
        $pet1 = new PetBooking(['transport_type' => 'cabin']);
        $this->assertEquals('В салоне под креслом (PETC)', $pet1->transportTypeName());

        $pet2 = new PetBooking(['transport_type' => 'seat']);
        $this->assertEquals('На отдельном месте (EUPS)', $pet2->transportTypeName());

        $pet3 = new PetBooking(['transport_type' => 'hold']);
        $this->assertEquals('В багажном отсеке (AVIH)', $pet3->transportTypeName());

        $pet4 = new PetBooking(['transport_type' => 'guide']);
        $this->assertEquals('Собака-поводырь (SVAN)', $pet4->transportTypeName());

        $petUnknown = new PetBooking(['transport_type' => 'spaceship']);
        $this->assertEquals('Перевозка питомца', $petUnknown->transportTypeName());
    }

    public function test_pet_booking_pet_type_names(): void
    {
        $dog = new PetBooking(['pet_type' => 'dog']);
        $this->assertEquals('Собака', $dog->petTypeName());

        $cat = new PetBooking(['pet_type' => 'cat']);
        $this->assertEquals('Кошка', $cat->petTypeName());

        $bird = new PetBooking(['pet_type' => 'bird']);
        $this->assertEquals('Птица', $bird->petTypeName());

        $other = new PetBooking(['pet_type' => 'turtle']);
        $this->assertEquals('Питомец', $other->petTypeName());
    }
}
