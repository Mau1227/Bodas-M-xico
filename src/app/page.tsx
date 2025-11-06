// /src/app/page.tsx
import { HeroSection } from '@/components/landing/HeroSection';
import { ComoFuncionaSection } from '@/components/landing/ComoFuncionaSection';
import { FeaturesSection } from '@/components/landing/FeaturesSection';
import { PricingSection } from '@/components/landing/PricingSection';
import { TestimonialsSection } from '@/components/landing/TestimonialSection';
import { FAQSection } from '@/components/landing/FAQSection';
// (También puedes importar y añadir la 'TemplatesSection' que te di antes)

export default function HomePage() {
  return (
    <>
      <HeroSection />
      <ComoFuncionaSection />
      <FeaturesSection />
      <PricingSection />
      <TestimonialsSection />
      <FAQSection />
    </>
  );
}