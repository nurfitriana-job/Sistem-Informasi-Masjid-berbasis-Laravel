 @props(['settings', 'colors'])
 <footer id="contact"
     style="background-image: url({{ asset($settings->site_logo ? '/storage/' . $settings->site_logo : 'assets/images/logo.png') }}); background-size: cover; background-repeat: no-repeat; background-position: center; background-color: {{ $colors['primary'] }}; ">
     <div class="container">
         <div class="footer-top">
             <div class="row">
                 <div class="col-lg-6 col-md-6">
                     <div class="footer-logo">
                         <img src="{{ asset($settings->site_logo ? '/storage/' . $settings->site_logo : 'assets/images/logo.png') }}"
                             alt="logo" class="w-25">
                         <h3>{{ $settings->seo_title ?? config('app.name') }}</h3>
                     </div>
                 </div>
                 <div class="col-lg-6 col-md-6">
                     <div class="connect-with">
                         <h4>Connect with us</h4>
                         <ul class="social-media">
                             @php
                                 $socialLinks = $settings->site_social_links;
                             @endphp
                             @foreach ($socialLinks as $key => $item)
                                 @php
                                     if (empty($item)) {
                                         continue;
                                     }
                                     $icon = '';
                                     switch ($key) {
                                         case 'tiktok':
                                             $icon = 'fa-brands fa-tiktok';
                                             break;
                                         case 'twitter':
                                             $icon = 'fab fa-twitter';
                                             break;
                                         case 'youtube':
                                             $icon = 'fa-brands fa-youtube';
                                             break;
                                         case 'facebook':
                                             $icon = 'fab fa-facebook-f';
                                             break;
                                         case 'instagram':
                                             $icon = 'fab fa-instagram';
                                             break;
                                     }
                                 @endphp
                                 <li>
                                     <a href="{{ $item }}" target="_blank" rel="noopener noreferrer">
                                         <i class="{{ $icon }}"></i></a>
                                 </li>
                             @endforeach
                         </ul>
                     </div>
                 </div>
             </div>
         </div>
         <div class="row Information pb-0">
             <div class="col-lg-6 col-md-6">
                 <div class="widget-title">
                     <h3>Informasi</h3>
                     <p>
                         {!! $settings->seo_description ?? '-' !!}
                     </p>
                 </div>
             </div>
             <div class="col-lg-6 col-md-6">
                 <div class="Information widget-title">
                     <h3>Contact info</h3>
                     <div class="contact-info">
                         <i class="fa-brands fa-whatsapp fa-2xl" style="color: {{ $colors['primary'] }}"></i>
                         <div>
                             <h5>Whatsapp:</h5>
                             <a href="">
                                 {{ $settings->site_support_phone ?? '0000000000' }}
                             </a>
                         </div>
                     </div>
                     <div class="contact-info">
                         <i class="fa-brands fa-telegram fa-2xl" style="color: {{ $colors['primary'] }}"></i>
                         <div>
                             <h5>Telegram:</h5>
                             <a href="https://t.me/{{ $settings->site_support_telegram ?? '' }}">
                                 {{ $settings->site_support_telegram ?? '@username' }}
                             </a>
                         </div>
                     </div>
                     <div class="contact-info">
                         <i class="fa-solid fa-location-dot fa-2xl" style="color: {{ $colors['primary'] }}"></i>
                         <h5>
                             Alamat: <br />
                             {!! $settings->site_address ?? 'Address not set' !!}
                         </h5>
                     </div>
                 </div>
             </div>
         </div>
         <div class="wpo-lower-footer">
             <p>Copyright ©<a href="#"><b> {{ $settings->site_name ?? config('app.name') }}</b></a>
                 {{ date('Y') }} . All
                 rights reserved.</p>
             <div class="d-flex align-items-center">
                 <a href="#"> Terms and Conditions</a>
                 <div class="border"></div>
                 <a href="#">Privacy Policy</a>
             </div>
         </div>
     </div>
 </footer>
