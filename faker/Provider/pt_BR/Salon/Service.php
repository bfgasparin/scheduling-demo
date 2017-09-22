<?php

namespace Faker\Provider\pt_BR\Salon;

use Faker\Provider\Base as BaseProvider;

/**
 * Provides salon related fake data
 * for Faker Generator for pt_BR
 *
 * @see Faker\Generator
 * @see Faker\Provider\Base
 */
class Service extends BaseProvider
{
    protected static $names = [
        "Corte Masculino",
        "Corte Masculino (Longo)",
        "Corte Feminino",
        "Corte Feminino (Médio)",
        "Corte Feminino (Longo)",
        "Corte Infantil",
        "Tintura (Curto)",
        "Tintura (Médio)",
        "Tintura (Longo)",
        "Drenagem Linfática",
        "Sobrancelhas (limpeza)",
        "Sobrancelhas (meio)",
        "Depilação Costas",
        "Depilação Coxa",
        "Depilação Nádegas",
        "Depilação Perna Inteira",
        "Depilação Ombros",
        "Depilação Virilha",
        "Massagem Esportiva",
        "Massagem Relaxante",
        "Quick Massage",
        "Reflexo (Curto)",
        "Reflexo (Médio)",
        "Reflexo (Longo)",
        "Escova Modeladora",
        "Manicure",
        "Pedicure",
        "Escova",
        "Alisamento",
        "Escova Progressiva",
        "Coloração",
        "Cauterização",
        "Reposição Unha Gel",
        "Mecha",
        "Maquiagem",
        "Peeling",
        "shiatsu",
        "Yoga",
        "Penteado",
        "SPA de mãos",
        "SPA de pés",
        "Fototerapia para acne",
        "Fototerapia para olheiras",
    ];

    /**
     * Get a salon service name
     *
     * @example Mair Moisturizing
     */
    public static function salonServiceName()
    {
        return static::randomElement(static::$names);
    }
}
