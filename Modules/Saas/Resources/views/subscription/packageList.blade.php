<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <title>{{ config('app.name', 'School ERP') }} - Pricing</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --secondary: #0ea5e9;
            --accent: #10b981;
        }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; }
        .hero-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white; padding: 80px 0 60px; text-align: center;
        }
        .hero-section h1 { font-size: 2.5rem; font-weight: 700; margin-bottom: 15px; }
        .hero-section p { font-size: 1.15rem; opacity: 0.9; max-width: 600px; margin: 0 auto; }
        .pricing-section { padding: 60px 0; }
        .pricing-card {
            background: white; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 40px 30px; text-align: center; transition: transform 0.3s, box-shadow 0.3s;
            position: relative; overflow: hidden; height: 100%;
        }
        .pricing-card:hover { transform: translateY(-8px); box-shadow: 0 12px 40px rgba(0,0,0,0.12); }
        .pricing-card.popular { border: 2px solid var(--primary); }
        .pricing-card.popular::before {
            content: 'Most Popular'; position: absolute; top: 0; right: 0;
            background: var(--primary); color: white; padding: 6px 20px;
            font-size: 0.75rem; font-weight: 600; border-radius: 0 14px 0 12px;
        }
        .plan-name { font-size: 1.3rem; font-weight: 600; color: #1e293b; margin-bottom: 8px; }
        .plan-price { font-size: 2.8rem; font-weight: 800; color: var(--primary); margin: 20px 0; }
        .plan-price span { font-size: 1rem; font-weight: 400; color: #64748b; }
        .plan-description { color: #64748b; font-size: 0.95rem; margin-bottom: 25px; min-height: 48px; }
        .plan-features { text-align: left; margin: 25px 0; padding: 0; list-style: none; }
        .plan-features li { padding: 8px 0; color: #475569; font-size: 0.92rem; display: flex; align-items: center; gap: 10px; }
        .plan-features li i { color: var(--accent); font-size: 0.85rem; }
        .btn-plan {
            width: 100%; padding: 12px 24px; border-radius: 8px; font-weight: 600;
            font-size: 1rem; border: none; cursor: pointer; transition: all 0.3s;
        }
        .btn-plan-primary { background: var(--primary); color: white; }
        .btn-plan-primary:hover { background: var(--primary-dark); color: white; }
        .btn-plan-outline { background: transparent; color: var(--primary); border: 2px solid var(--primary); }
        .btn-plan-outline:hover { background: var(--primary); color: white; }
        .trial-badge {
            display: inline-block; background: #dbeafe; color: var(--primary);
            padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;
            margin-top: 10px;
        }
        .comparison-section { padding: 60px 0; background: white; }
        .section-title { text-align: center; font-size: 2rem; font-weight: 700; color: #1e293b; margin-bottom: 50px; }
        .table-comparison { border-radius: 12px; overflow: hidden; box-shadow: 0 2px 16px rgba(0,0,0,0.06); }
        .table-comparison th { background: #f1f5f9; padding: 16px 20px; font-weight: 600; color: #334155; }
        .table-comparison td { padding: 14px 20px; vertical-align: middle; }
        .check-icon { color: var(--accent); }
        .cross-icon { color: #ef4444; }
    </style>
</head>
<body>
    <section class="hero-section">
        <div class="container">
            <h1>Choose Your Plan</h1>
            <p>Flexible pricing for schools of all sizes. Start with a 14-day free trial, no credit card required.</p>
        </div>
    </section>

    <section class="pricing-section">
        <div class="container">
            <div class="row g-4 justify-content-center">
                @foreach($plans as $index => $plan)
                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card {{ $index === 1 ? 'popular' : '' }}">
                        <div class="plan-name">{{ $plan->name }}</div>
                        <div class="plan-description">{{ $plan->features }}</div>
                        <div class="plan-price">
                            ${{ number_format($plan->price, 2) }}
                            <span>/ month</span>
                        </div>
                        <ul class="plan-features">
                            <li><i class="fas fa-check-circle check-icon"></i> {{ $plan->staff_quantity }} Teacher(s)</li>
                            <li><i class="fas fa-check-circle check-icon"></i> {{ $plan->student_quantity }} Students</li>
                            @if($plan->modules)
                                @foreach($plan->modules as $module)
                                <li><i class="fas fa-check-circle check-icon"></i> {{ ucfirst(str_replace('_', ' ', $module)) }}</li>
                                @endforeach
                            @endif
                            @if($plan->trial_days > 0)
                            <li><i class="fas fa-check-circle check-icon"></i> {{ $plan->trial_days }}-day free trial</li>
                            @endif
                        </ul>
                        <a href="{{ route('subscription.checkout', ['plan' => $plan->id]) }}" class="btn btn-plan {{ $index === 1 ? 'btn-plan-primary' : 'btn-plan-outline' }}">
                            Get Started
                        </a>
                        @if($plan->trial_days > 0)
                        <div class="trial-badge">Free trial included</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="comparison-section">
        <div class="container">
            <h2 class="section-title">Feature Comparison</h2>
            <div class="table-responsive">
                <table class="table table-comparison">
                    <thead>
                        <tr>
                            <th>Feature</th>
                            @foreach($plans as $plan)
                            <th class="text-center">{{ $plan->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Monthly Price</td>
                            @foreach($plans as $plan)
                            <td class="text-center">${{ number_format($plan->price, 2) }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Free Trial</td>
                            @foreach($plans as $plan)
                            <td class="text-center">{{ $plan->trial_days }} days</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Teachers</td>
                            @foreach($plans as $plan)
                            <td class="text-center">{{ $plan->staff_quantity }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Students</td>
                            @foreach($plans as $plan)
                            <td class="text-center">{{ number_format($plan->student_quantity) }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Modules Included</td>
                            @foreach($plans as $plan)
                            <td class="text-center">{{ count($plan->modules ?? []) }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td>Priority Support</td>
                            <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                            <td class="text-center"><i class="fas fa-times cross-icon"></i></td>
                            <td class="text-center"><i class="fas fa-check-circle check-icon"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
