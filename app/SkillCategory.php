<?php 

namespace App;

abstract class SkillCategory{
    const FUNDAMENTAL = 1;
    const FUNDAMENTAL_DETAIL = "Basic knowledge";
    const NOVICE = 2;
    const NOVICE_DETAIL = "Limited experience";
    const INTERMEDIATE = 3;    
    const INTERMEDIATE_DETAIL = "Practical application";
    const ADVANCED = 4;    
    const ADVANCED_DETAIL = "Applied theory";
    const EXPERT = 5;    
    const EXPERT_DETAIL = "Recognized authority";
}