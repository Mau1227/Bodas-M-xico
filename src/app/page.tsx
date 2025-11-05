// /src/app/page.tsx
import { HeroSection } from '@/components/landing/HeroSection';
import { ComoFuncionaSection } from '@/components/landing/ComoFuncionaSection';
// Importarás más secciones aquí
// import { FeaturesSection } from '@/components/landing/FeaturesSection';
// import { PricingSection } from '@/components/landing/PricingSection';

export default function HomePage() {
  return (
    <>
      {/* 1. Sección Hero */}
      <HeroSection />
      <ComoFuncionaSection />

      {/* 2. Sección "Cómo Funciona" (Tu próximo paso) */}
      {/* <HowItWorksSection /> */}

      {/* 3. Sección "Características" (Tu próximo paso) */}
      {/* <FeaturesSection /> */}

      {/* 4. Sección "Plantillas" (Tu próximo paso) */}
      {/* <TemplatesSection /> */}

      {/* 5. Sección "Precios" (Tu próximo paso) */}
      {/* <PricingSection /> */}

      {/* 6. Sección "FAQ" (Tu próximo paso) */}
      {/* <FAQSection /> */}
    </>
  );
}