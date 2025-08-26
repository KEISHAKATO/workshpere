<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    public function definition(): array
    {
        $categories = ['construction','tailoring','delivery','cleaning','electrical','plumbing'];
        $skillsByCat = [
            'construction' => ['carpentry','masonry'],
            'tailoring'    => ['tailoring'],
            'delivery'     => ['driving'],
            'cleaning'     => ['cleaning'],
            'electrical'   => ['electrical wiring'],
            'plumbing'     => ['plumbing'],
        ];
        $cities  = ['Nairobi','Mombasa','Kisumu','Nakuru','Eldoret','Thika'];
        $counties = ['Nairobi','Mombasa','Kisumu','Nakuru','Uasin Gishu','Kiambu'];

        $cat = $this->faker->randomElement($categories);

        $roleNouns = [
            'Skilled Carpenter','Experienced Plumber','Professional Driver','Tailor',
            'Electrician','General Cleaner','Mason'
        ];
        $title = $this->faker->randomElement($roleNouns).' Needed';

        $desc = $this->faker->randomElement([
            'We need a reliable professional to support a short project.',
            'Looking for a responsible worker to assist with daily tasks.',
            'Join our team for a hands-on assignment with fair pay.',
            'Seeking someone careful, punctual and focused on quality.',
        ]);

        return [
            'title'            => $title,
            'description'      => $desc,
            'category'         => $cat,
            'job_type'         => $this->faker->randomElement(['gig','contract','full_time','part_time']),
            'pay_min'          => $this->faker->numberBetween(800, 3000),
            'pay_max'          => $this->faker->numberBetween(3001, 8000),
            'currency'         => 'KES',
            'location_city'    => $this->faker->randomElement($cities),
            'location_county'  => $this->faker->randomElement($counties),
            'lat'              => null,
            'lng'              => null,
            'required_skills'  => $skillsByCat[$cat],
            'status'           => 'open',
            'posted_at'        => now(),
        ];
    }
}
