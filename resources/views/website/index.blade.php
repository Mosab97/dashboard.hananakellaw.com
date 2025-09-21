@extends('website.layout')


@section('content')
    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="display-4 fw-bold mb-4">Transform Your Ideas Into Reality</h1>
                    <p class="lead mb-4">Join SQU Innovation Program and be part of the next generation of innovators.
                        Apply now for our latest programs and bring your ideas to life.</p>
                    <a href="#programs" class="btn btn-light btn-lg">Explore Programs</a>
                </div>
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=600" alt="Innovation"
                        class="img-fluid rounded shadow" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Programs Section -->
    <section class="py-5" id="programs">
        <div class="container">
            <h2 class="text-center mb-5">Current Programs</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card program-card">
                        <img src="https://images.unsplash.com/photo-1504384764586-bb4cdc1707b0?w=400" class="card-img-top"
                            alt="Hackathon" loading="lazy">
                        <div class="card-body">
                            <h5 class="card-title">Innovation Hackathon 2025</h5>
                            <p class="card-text">48-hour challenge to build innovative solutions for real-world
                                problems.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-success">Applications Open</span>
                                <small class="text-muted">Deadline: Mar 15, 2025</small>
                            </div>
                            <a href="#" class="btn btn-primary mt-3 w-100">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card program-card">
                        <img src="https://images.unsplash.com/photo-1559136555-9303baea8ebd?w=400" class="card-img-top"
                            alt="Startup" loading="lazy">
                        <div class="card-body">
                            <h5 class="card-title">Tech Startup Fund</h5>
                            <p class="card-text">Funding and mentorship for technology startups in early stages.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-warning">Coming Soon</span>
                                <small class="text-muted">Opens: Apr 1, 2025</small>
                            </div>
                            <a href="#" class="btn btn-primary mt-3 w-100">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card program-card">
                        <img src="https://images.unsplash.com/photo-1507668077129-56e32842fcaa?w=400" class="card-img-top"
                            alt="Research" loading="lazy">
                        <div class="card-body">
                            <h5 class="card-title">Research Innovation Grant</h5>
                            <p class="card-text">Supporting innovative research projects in various fields.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-success">Applications Open</span>
                                <small class="text-muted">Deadline: May 1, 2025</small>
                            </div>
                            <a href="#" class="btn btn-primary mt-3 w-100">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Meet Our Mentors</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card mentor-card">
                        <img src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=400" class="card-img-top"
                            alt="Mentor 1" loading="lazy">
                        <div class="card-body text-center">
                            <h5 class="card-title">Dr. Sarah Chen</h5>
                            <p class="card-text">AI & Machine Learning Expert</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mentor-card">
                        <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400" class="card-img-top"
                            alt="Mentor 2" loading="lazy">
                        <div class="card-body text-center">
                            <h5 class="card-title">Dr. Ahmed Al-Said</h5>
                            <p class="card-text">Software Architecture Expert</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mentor-card">
                        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400" class="card-img-top"
                            alt="Mentor 3" loading="lazy">
                        <div class="card-body text-center">
                            <h5 class="card-title">Dr. Lisa Wang</h5>
                            <p class="card-text">Product Development Lead</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
