<?php

/* modules/contrib/slick/templates/slick-slide.html.twig */
class __TwigTemplate_322be33e1c4d38aa7001c9464d77badb6876994e9bd98175d4ae11deb68c1980 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
            'slick_slide' => [$this, 'block_slick_slide'],
            'slick_caption' => [$this, 'block_slick_caption'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453 = $this->env->getExtension("Drupal\\webprofiler\\Twig\\Extension\\ProfilerExtension");
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453->enter($__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "modules/contrib/slick/templates/slick-slide.html.twig"));

        $tags = ["set" => 21, "block" => 35, "if" => 44];
        $filters = ["clean_class" => 24];
        $functions = [];

        try {
            $this->env->getExtension('Twig_Extension_Sandbox')->checkSecurity(
                ['set', 'block', 'if'],
                ['clean_class'],
                []
            );
        } catch (Twig_Sandbox_SecurityError $e) {
            $e->setSourceContext($this->getSourceContext());

            if ($e instanceof Twig_Sandbox_SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof Twig_Sandbox_SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof Twig_Sandbox_SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

        // line 21
        $context["classes"] = [0 => "slick__slide", 1 => "slide", 2 => ("slide--" .         // line 22
($context["delta"] ?? null)), 3 => ((twig_test_empty($this->getAttribute(        // line 23
($context["item"] ?? null), "slide", []))) ? ("slide--text") : ("")), 4 => (($this->getAttribute(        // line 24
($context["settings"] ?? null), "layout", [])) ? (("slide--caption--" . \Drupal\Component\Utility\Html::getClass($this->getAttribute(($context["settings"] ?? null), "layout", [])))) : ("")), 5 => (($this->getAttribute(        // line 25
($context["settings"] ?? null), "class", [])) ? ($this->getAttribute(($context["settings"] ?? null), "class", [])) : (""))];
        // line 29
        $context["content_classes"] = [0 => (($this->getAttribute(        // line 30
($context["settings"] ?? null), "detroy", [])) ? ("slide") : ("")), 1 => (( !$this->getAttribute(        // line 31
($context["settings"] ?? null), "detroy", [])) ? ("slide__content") : (""))];
        // line 34
        ob_start();
        // line 35
        echo "  ";
        $this->displayBlock('slick_slide', $context, $blocks);
        $context["slide"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 43
        echo "
";
        // line 44
        if ($this->getAttribute(($context["settings"] ?? null), "wrapper", [])) {
            // line 45
            echo "  <div";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["attributes"] ?? null), "addClass", [0 => ($context["classes"] ?? null)], "method"), "html", null, true));
            echo ">
  ";
            // line 46
            if (twig_test_empty($this->getAttribute(($context["settings"] ?? null), "grid", []))) {
                echo "<div";
                echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["content_attributes"] ?? null), "addClass", [0 => ($context["content_classes"] ?? null)], "method"), "html", null, true));
                echo ">";
            }
        }
        // line 48
        echo "
  ";
        // line 49
        if ($this->getAttribute(($context["item"] ?? null), "slide", [])) {
            // line 50
            echo "    ";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["slide"] ?? null), "html", null, true));
            echo "
  ";
        }
        // line 52
        echo "
  ";
        // line 53
        if ($this->getAttribute(($context["item"] ?? null), "caption", [])) {
            // line 54
            echo "    ";
            $this->displayBlock('slick_caption', $context, $blocks);
            // line 82
            echo "  ";
        }
        // line 83
        echo "
";
        // line 84
        if ($this->getAttribute(($context["settings"] ?? null), "wrapper", [])) {
            // line 85
            echo "  ";
            if (twig_test_empty($this->getAttribute(($context["settings"] ?? null), "grid", []))) {
                echo "</div>";
            }
            // line 86
            echo "  </div>
";
        }
        
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453->leave($__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453_prof);

    }

    // line 35
    public function block_slick_slide($context, array $blocks = [])
    {
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453 = $this->env->getExtension("Drupal\\webprofiler\\Twig\\Extension\\ProfilerExtension");
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453->enter($__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "slick_slide"));

        // line 36
        echo "    ";
        if (($this->getAttribute(($context["settings"] ?? null), "split", []) &&  !$this->getAttribute(($context["settings"] ?? null), "unslick", []))) {
            // line 37
            echo "      <div class=\"slide__media\">";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["item"] ?? null), "slide", []), "html", null, true));
            echo "</div>
    ";
        } else {
            // line 39
            echo "      ";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute(($context["item"] ?? null), "slide", []), "html", null, true));
            echo "
    ";
        }
        // line 41
        echo "  ";
        
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453->leave($__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453_prof);

    }

    // line 54
    public function block_slick_caption($context, array $blocks = [])
    {
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453 = $this->env->getExtension("Drupal\\webprofiler\\Twig\\Extension\\ProfilerExtension");
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453->enter($__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "slick_caption"));

        // line 55
        echo "      ";
        if ($this->getAttribute(($context["settings"] ?? null), "fullwidth", [])) {
            echo "<div class=\"slide__constrained\">";
        }
        // line 56
        echo "
        <div class=\"slide__caption\">
          ";
        // line 58
        if ($this->getAttribute($this->getAttribute(($context["item"] ?? null), "caption", []), "overlay", [])) {
            // line 59
            echo "            <div class=\"slide__overlay\">";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute($this->getAttribute(($context["item"] ?? null), "caption", []), "overlay", []), "html", null, true));
            echo "</div>
            ";
            // line 60
            if ($this->getAttribute(($context["settings"] ?? null), "data", [])) {
                echo "<div class=\"slide__data\">";
            }
            // line 61
            echo "          ";
        }
        // line 62
        echo "
          ";
        // line 63
        if ($this->getAttribute($this->getAttribute(($context["item"] ?? null), "caption", []), "title", [])) {
            // line 64
            echo "            <h2 class=\"slide__title\">";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute($this->getAttribute(($context["item"] ?? null), "caption", []), "title", []), "html", null, true));
            echo "</h2>
          ";
        }
        // line 66
        echo "
          ";
        // line 67
        if ($this->getAttribute($this->getAttribute(($context["item"] ?? null), "caption", []), "alt", [])) {
            // line 68
            echo "            <p class=\"slide__description\">";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute($this->getAttribute(($context["item"] ?? null), "caption", []), "alt", []), "html", null, true));
            echo "</p>
          ";
        }
        // line 70
        echo "
          ";
        // line 71
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute($this->getAttribute(($context["item"] ?? null), "caption", []), "data", []), "html", null, true));
        echo "

          ";
        // line 73
        if ($this->getAttribute($this->getAttribute(($context["item"] ?? null), "caption", []), "link", [])) {
            // line 74
            echo "            <div class=\"slide__link\">";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute($this->getAttribute(($context["item"] ?? null), "caption", []), "link", []), "html", null, true));
            echo "</div>
          ";
        }
        // line 76
        echo "
          ";
        // line 77
        if (($this->getAttribute($this->getAttribute(($context["item"] ?? null), "caption", []), "overlay", []) && $this->getAttribute(($context["settings"] ?? null), "data", []))) {
            echo "</div>";
        }
        // line 78
        echo "        </div>

      ";
        // line 80
        if ($this->getAttribute(($context["settings"] ?? null), "fullwidth", [])) {
            echo "</div>";
        }
        // line 81
        echo "    ";
        
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453->leave($__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453_prof);

    }

    public function getTemplateName()
    {
        return "modules/contrib/slick/templates/slick-slide.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  232 => 81,  228 => 80,  224 => 78,  220 => 77,  217 => 76,  211 => 74,  209 => 73,  204 => 71,  201 => 70,  195 => 68,  193 => 67,  190 => 66,  184 => 64,  182 => 63,  179 => 62,  176 => 61,  172 => 60,  167 => 59,  165 => 58,  161 => 56,  156 => 55,  150 => 54,  143 => 41,  137 => 39,  131 => 37,  128 => 36,  122 => 35,  113 => 86,  108 => 85,  106 => 84,  103 => 83,  100 => 82,  97 => 54,  95 => 53,  92 => 52,  86 => 50,  84 => 49,  81 => 48,  74 => 46,  69 => 45,  67 => 44,  64 => 43,  60 => 35,  58 => 34,  56 => 31,  55 => 30,  54 => 29,  52 => 25,  51 => 24,  50 => 23,  49 => 22,  48 => 21,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "modules/contrib/slick/templates/slick-slide.html.twig", "/var/www/html/saretec/web/modules/contrib/slick/templates/slick-slide.html.twig");
    }
}
