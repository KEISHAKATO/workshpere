<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    public function definition(): array
    {
        $skillsPool = [
            'carpentry','welding','plumbing','electrical wiring','painting',
            'tailoring','masonry','driving','cleaning','gardening'
        ];
        shuffle($skillsPool);
        $skills = array_slice($skillsPool, 0, rand(1, 3));

        $cities  = ['Nairobi','Mombasa','Kisumu','Nakuru','Eldoret','Thika'];
        $counties = ['Nairobi','Mombasa','Kisumu','Nakuru','Uasin Gishu','Kiambu'];

        return [
            // clear, short English text
            'bio'               => $this->faker->randomElement([
                'Reliable and detail-oriented professional.',
                'Hard-working and punctual with strong teamwork skills.',
                'Self-motivated worker focused on quality results.',
                'Customer-friendly and adaptable to changing needs.',
            ]),
            'skills'            => $skills,
            'experience_years'  => $this->faker->numberBetween(0, 8),
            'preferred_job_type'=> $this->faker->randomElement(['gig','contract','full_time','part_time']),
            'availability'      => $this->faker->randomElement(['immediate','1_week','2_weeks','flexible']),

            'company_name'      => null,
            'website'           => null,

            'location_city'     => $this->faker->randomElement($cities),
            'location_county'   => $this->faker->randomElement($counties),
            'lat'               => null,
            'lng'               => null,
        ];
    }
}
