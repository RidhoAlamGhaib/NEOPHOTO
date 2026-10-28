<?php

it('shows the public company profile on the about page', function () {
    $response = $this->get(route('about'));

    $response->assertSeeText('Merayakan momen dengan sentuhan Korea.');
    $response->assertSeeText('27 Juni 2023');
    $response->assertSeeText('Kota Kasablanka');
    $response->assertSeeText('neophotoindonesia@gmail.com');
});

it('links the homepage to the about page', function () {
    $response = $this->get(route('index'));

    $response->assertSee(route('about'));
});
