<?php

/* modules/contrib/devel/webprofiler/templates/Profiler/webprofiler_loader.html.twig */
class __TwigTemplate_f1dfb51cbb997ce9dcb27ee7e113c4b76e05c36bd8ba6ad18f4017df1da20305 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453 = $this->env->getExtension("Drupal\\webprofiler\\Twig\\Extension\\ProfilerExtension");
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453->enter($__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "modules/contrib/devel/webprofiler/templates/Profiler/webprofiler_loader.html.twig"));

        $tags = [];
        $filters = [];
        $functions = [];

        try {
            $this->env->getExtension('Twig_Extension_Sandbox')->checkSecurity(
                [],
                [],
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

        // line 1
        echo "<div id=\"webprofiler";
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["token"] ?? null), "html", null, true));
        echo "\" class=\"sf-toolbar\" style=\"display: none\"></div>

<script>
    Webprofiler = (function () {
        \"use strict\";

        var key = 'webprofiler/profiler/',

                getPreference = function (name) {
                    if (!window.localStorage) {
                        return null;
                    }

                    return localStorage.getItem(key + name);
                },

                setPreference = function (name, value) {
                    if (!window.localStorage) {
                        return null;
                    }

                    localStorage.setItem(key + name, value);
                    return true;
                },

                load = function () {
                    ajax('";
        // line 27
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["profiler_url"] ?? null), "html", null, true));
        echo "', function (result) {
                        var toolbar = document.getElementById('webprofiler";
        // line 28
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["token"] ?? null), "html", null, true));
        echo "');
                        toolbar.innerHTML = result;
                        toolbar.style.display = 'block';

                        if (getPreference('toolbar/displayState') == 'none') {
                            document.getElementById('sfToolbarMainContent-";
        // line 33
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["token"] ?? null), "html", null, true));
        echo "').style.display = 'none';
                            document.getElementById('sfToolbarClearer-";
        // line 34
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["token"] ?? null), "html", null, true));
        echo "').style.display = 'none';
                            document.getElementById('sfMiniToolbar-";
        // line 35
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["token"] ?? null), "html", null, true));
        echo "').style.display = 'block';
                        } else {
                            document.getElementById('sfToolbarMainContent-";
        // line 37
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["token"] ?? null), "html", null, true));
        echo "').style.display = 'block';
                            document.getElementById('sfToolbarClearer-";
        // line 38
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["token"] ?? null), "html", null, true));
        echo "').style.display = 'block';
                            document.getElementById('sfMiniToolbar-";
        // line 39
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["token"] ?? null), "html", null, true));
        echo "').style.display = 'none';
                        }

                        var arr = toolbar.getElementsByTagName('script');
                        for (var n = 0; n < arr.length; n++) {
                            eval(arr[n].innerHTML);
                        }
                    });
                },

                ajax = function (url, callback, data) {
                    try {
                        var x = new (XMLHttpRequest || ActiveXObject)('MSXML2.XMLHTTP.3.0');
                        x.open(data ? 'POST' : 'GET', url, 1);
                        x.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        x.setRequestHeader('Content-type', 'application/json');
                        x.onreadystatechange = function () {
                            x.readyState > 3 && x.status === 200 && callback && callback(x.responseText, x);
                        };
                        x.send(data);
                    } catch (e) {
                        window.console && console.log(e);
                    }
                };

        return {
            getPreference: getPreference,
            setPreference: setPreference,
            load: load,
            ajax: ajax
        }
    })();

    Webprofiler.load();
</script>
";
        
        $__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453->leave($__internal_b8a44bb7188f10fa054f3681425c559c29de95cd0490f5c67a67412aafc0f453_prof);

    }

    public function getTemplateName()
    {
        return "modules/contrib/devel/webprofiler/templates/Profiler/webprofiler_loader.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  109 => 39,  105 => 38,  101 => 37,  96 => 35,  92 => 34,  88 => 33,  80 => 28,  76 => 27,  46 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "modules/contrib/devel/webprofiler/templates/Profiler/webprofiler_loader.html.twig", "/var/www/html/saretec/web/modules/contrib/devel/webprofiler/templates/Profiler/webprofiler_loader.html.twig");
    }
}
